<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\PembayaranHutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranHutangController extends Controller
{
    public function index()
    {
        $transaksi = BarangMasuk::with('mitra')
            ->where('status_pembayaran', 'HUTANG')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('admin.pembayaran_hutang.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = BarangMasuk::with(['mitra', 'items.product', 'pembayaranHutang'])
            ->findOrFail($id);

        return view('admin.pembayaran_hutang.show', compact('transaksi'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'barang_masuk_id' => 'required|exists:barang_masuk,id',
        'tanggal_bayar'   => 'required|date',
        'jumlah_bayar'    => 'required|numeric|min:1',
        'metode_bayar'    => 'nullable|string|max:50',
    ]);

    DB::transaction(function () use ($validated) {

        $transaksi = BarangMasuk::lockForUpdate()
            ->findOrFail($validated['barang_masuk_id']);

        // Hitung total yang sudah dibayar sebelumnya
        $totalSebelumnya = $transaksi->pembayaranHutang()
            ->sum('jumlah_bayar');

        $sisaHutang = $transaksi->total_harga - $totalSebelumnya;

        // 🔴 Validasi agar tidak bayar melebihi hutang
        if ($validated['jumlah_bayar'] > $sisaHutang) {
            throw new \Exception('Jumlah pembayaran melebihi sisa hutang.');
        }

        // Simpan pembayaran
        PembayaranHutang::create($validated);

        $totalBayarBaru = $totalSebelumnya + $validated['jumlah_bayar'];
        $sisaBaru = $transaksi->total_harga - $totalBayarBaru;

        $transaksi->update([
            'total_dibayar' => $totalBayarBaru,
            'sisa_hutang' => $sisaBaru, // jika kolom ini ada
            'status_pembayaran' => $sisaBaru <= 0 ? 'LUNAS' : 'HUTANG',
        ]);
    });

    return back()->with('success', 'Pembayaran hutang berhasil dicatat.');
}

public function bayarHutang(Request $request, $id)
{
    $request->validate([
        'tanggal_bayar' => 'required|date',
        'jumlah_bayar'  => 'required|numeric|min:1',
        'metode_bayar'  => 'nullable|string|max:50',
    ]);

    DB::transaction(function () use ($request, $id) {

        $transaksi = BarangMasuk::findOrFail($id);

        $sisa = $transaksi->total_harga - $transaksi->total_dibayar;

        // 🔒 Jangan boleh bayar melebihi sisa hutang
        if ($request->jumlah_bayar > $sisa) {
            abort(400, 'Jumlah bayar melebihi sisa hutang.');
        }

        PembayaranHutang::create([
            'barang_masuk_id' => $transaksi->id,
            'tanggal_bayar'   => $request->tanggal_bayar,
            'jumlah_bayar'    => $request->jumlah_bayar,
            'metode_bayar'    => $request->metode_bayar,
        ]);

        $transaksi->increment('total_dibayar', $request->jumlah_bayar);

        if ($transaksi->total_dibayar >= $transaksi->total_harga) {
            $transaksi->update([
                'status_pembayaran' => 'LUNAS'
            ]);
        }
    });

    return back()->with('success', 'Pembayaran hutang berhasil disimpan.');
}
}
