# Panduan Implementasi: Arsitektur Offline-First POS (React + Laravel)

Dokumen ini berisi rangkuman teknis tentang cara membangun fitur **Offline-First Synchronization** untuk aplikasi Kasir (Point of Sale) yang tahan terhadap sinyal buruk, koneksi putus, dan masalah *cache* Service Worker. Anda dapat menggunakan panduan ini untuk mereplikasi fitur serupa di aplikasi kasir Anda yang lain.

---

## 1. Konsep Dasar (Arsitektur)
Dalam sistem *Offline-First*, **Frontend adalah Master** saat *offline*. Server bertugas untuk menerima dan merekonsiliasi (menyesuaikan) data setelah koneksi kembali.
*   **Penyimpanan Lokal:** Menggunakan **Dexie.js** (IndexedDB) sebagai *database lokal* di dalam browser perangkat kasir.
*   **Antrean (Queueing):** Transaksi tidak langsung dikirim ke server lewat *API Call* biasa, melainkan disimpan dulu di tabel `sync_queue` di IndexedDB.
*   **Latar Belakang (Background Sync):** Sebuah fungsi *hook* (misalnya `usePosSync`) secara berkala mengecek antrean ini dan mengirimkannya ke server saat internet tersedia.

---

## 2. Persiapan Database (Backend)

Anda wajib menambahkan kolom pengaman di tabel `orders` (transaksi) Anda.

### A. Tambahkan `client_uuid` (Wajib!)
Berfungsi sebagai KTP unik untuk setiap transaksi yang dibuat secara *offline*, guna **mencegah duplikasi (double entry)** saat koneksi melambat/putus di tengah jalan.
```php
// Di dalam file migration create_orders_table atau add_client_uuid_to_orders
$table->string('client_uuid')->nullable()->unique()->after('id');
```
*Jangan lupa tambahkan `client_uuid` ke dalam `$fillable` di Model `Order`.*

### B. Izinkan Stok Negatif untuk POS (Wajib!)
Karena barang secara fisik sudah berpindah tangan ke pelanggan (kasir offline), server tidak boleh menolak transaksi meskipun stok di *database* sudah 0. Transaksi harus tetap masuk, dan biarkan stok menjadi minus.

---

## 3. Logika Frontend (React + Dexie.js)

### A. Skema IndexedDB (Dexie)
Buat struktur database lokal. Minimal harus ada tabel untuk katalog, keranjang/draft, pelanggan lokal, dan antrean sinkronisasi.
```javascript
// db.ts
import Dexie from 'dexie';

export const db = new Dexie('POSDatabase');
db.version(1).stores({
    products: 'id, name, barcode, category_id', // Cache katalog
    customers: 'id, name, phone',               // Cache pelanggan
    sync_queue: 'client_uuid, type, status, retry_count, payload' // Antrean
});
```

### B. Proses Checkout Kasir
Saat kasir menekan "Bayar", data **JANGAN** dikirim via Axios! Masukkan ke IndexedDB.
```javascript
const clientUuid = generateUUID(); // Buat ID unik (contoh: crypto.randomUUID())

await db.sync_queue.put({
    client_uuid: clientUuid,
    type: 'order',
    status: 'pending', 
    retry_count: 0,
    payload: {
        client_uuid: clientUuid,
        items: [...],
        paid: 100000,
        customer_id: customerId // Bisa ID sementara (angka negatif) jika customer baru
    }
});
```

---

## 4. Mesin Sinkronisasi (Sync Engine)

Buat *Custom Hook* (`useSync.ts`) yang berjalan di latar belakang (misalnya dengan `setInterval` setiap 5 detik, atau dipicu event `window.addEventListener('online')`).

**Alur Kerja Sinkronisasi:**
1. Ambil semua antrean: `db.sync_queue.where('status').equals('pending').toArray()`
2. *Looping* antrean satu per satu.
3. Cek apakah ada Data Baru (seperti Pelanggan Baru yang ID-nya masih sementara/negatif). Jika ada, sinkronkan pelanggan tersebut lebih dulu, lalu ubah ID negatif menjadi ID asli dari server.
4. Kirim *payload* transaksi ke server menggunakan Axios/Fetch.
5. Jika berhasil (Status 200), **Hapus** transaksi dari `sync_queue`.
6. Jika gagal (misal server down), naikkan `retry_count`. Jika `retry_count` > 3, ubah status menjadi `failed` agar bisa di-*retry* manual oleh kasir.

---

## 5. Logika Backend (Laravel)

Di dalam Controller Laravel (`PosSyncController@store`), ada 3 aturan emas yang harus diterapkan:

### A. Anti-Duplikasi (Race Condition & Retry Catch)
Gunakan `client_uuid` untuk memastikan transaksi tidak tersimpan dua kali.
```php
$existingOrder = Order::withTrashed()->where('client_uuid', $request->client_uuid)->first();

// Jika sudah ada, abaikan saja (tapi pura-pura berikan respons sukses ke frontend)
if ($existingOrder) {
    return response()->json([
        'status' => 'success',
        'message' => 'Transaction already processed',
    ]);
}
```

### B. Tangkap Exception 1062 (Super Race Condition)
Jika ada 2 *request* yang masuk dalam milidetik yang sama, database akan memblokir *request* kedua dengan error `1062 Duplicate Entry`. Tangkap error ini dan anggap sukses.
```php
} catch (\Exception $e) {
    if ($e instanceof \Illuminate\Database\QueryException && $e->errorInfo[1] == 1062) {
        return response()->json(['status' => 'success']); // Anggap saja sudah sukses!
    }
    // ...
}
```

### C. Bypass CSRF untuk PWA
Jika Anda menggunakan Laravel dengan sistem *session/cookie* biasa (web.php), Service Worker milik PWA sering menyimpan (*cache*) file HTML yang berisi `csrf-token` lama. Akibatnya, API sering menolak *request* dengan error 419 Page Expired.
**Solusi:** Kecualikan URL sinkronisasi dari verifikasi CSRF di `bootstrap/app.php` (Laravel 11) atau `VerifyCsrfToken.php` (Laravel 10 ke bawah).
```php
$middleware->validateCsrfTokens(except: [
    'api/pos/sync',
    'api/customers/sync'
]);
```

---

## 6. Skenario Penanganan Kegagalan (Edge Cases)

| Masalah / Skenario | Solusi yang Diterapkan |
| :--- | :--- |
| Kasir menambah "Pelanggan Baru" saat offline, belum punya ID dari database server. | Berikan ID negatif lokal (misal: `-1`). Saat *online*, kirim data pelanggan ke server dulu, dapatkan ID asli (misal: `42`), lalu **timpa** ID negatif di keranjang dengan ID asli sebelum keranjang dikirim. |
| Barang yang dibeli ternyata sudah dihapus oleh Admin di pusat. | Frontend menangkap HTTP 400 (Bad Request). Transaksi diubah statusnya menjadi `failed` di IndexedDB. Sediakan tombol **"Retry / Edit"** bagi kasir untuk mengganti barang tersebut. |
| Internet mati total setelah kasir klik "Bayar". | Karena PWA dan IndexedDB, UI langsung merespons "Berhasil!". Transaksi diam-diam mengantre di *background*. Begitu WiFi menyala kembali, transaksi dikirim otomatis tanpa kasir sadari. |
