@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">
            {{ isset($isEdit) && $isEdit ? '✏️ Edit Barang' : '➕ Tambah Barang' }}
        </h2>
        <a href="{{ route('admin.products.index') }}"
           class="text-sm text-gray-600 hover:text-blue-600 transition">
            ← Kembali ke daftar
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white p-8 rounded-xl shadow-sm border">

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form 
            action="{{ isset($isEdit) && $isEdit 
                ? route('admin.products.update', $product->id) 
                : route('admin.products.store') }}" 
            method="POST" 
            class="space-y-6"
        >
            @csrf
            @if(isset($isEdit) && $isEdit)
                @method('PUT')
            @endif

            <!-- Barcode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                <div class="flex gap-2">
                    <input type="text" id="barcode" name="barcode"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Scan barcode atau ketik manual"
                        value="{{ old('barcode', $product->barcode ?? '') }}" required>

                    <button type="button" onclick="openScanner()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg
                               hover:bg-blue-700 transition">
                        📷 Scan
                    </button>
                </div>
            </div>

            <!-- Scanner -->
            <div id="scanner-container" class="hidden border rounded-lg p-4 bg-gray-50">
                <video id="scanner" class="w-full rounded-lg"></video>
                <button type="button" onclick="closeScanner()"
                    class="mt-3 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg
                           hover:bg-red-700 transition">
                    ❌ Tutup Kamera
                </button>
            </div>

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input type="text" id="name" name="name"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    value="{{ old('name', $product->name ?? '') }}" required>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Harga & Stok -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                    <input type="number" id="purchase_price" name="purchase_price"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('purchase_price', $product->purchase_price ?? '') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                    <input type="number" id="price" name="price"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('price', $product->price ?? '') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stock"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('stock', $product->stock ?? '') }}" required>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.products.index') }}"
                   class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </a>

                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    💾 {{ isset($isEdit) && $isEdit ? 'Update Barang' : 'Simpan Barang' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
let codeReader;
let scanning = false;

function openScanner() {
    const container = document.getElementById('scanner-container');
    container.classList.remove('hidden');

    codeReader = new ZXing.BrowserMultiFormatReader();
    scanning = true;

    codeReader.decodeFromVideoDevice(null, 'scanner', (result, err) => {
        if (result && scanning) {
            scanning = false;

            const barcode = result.text;
            document.getElementById('barcode').value = barcode;

            beep();
            closeScanner();
            fetchProductByBarcode(barcode);
        }
    }).catch(err => {
        alert("Tidak bisa mengakses kamera. Periksa izin browser.");
        console.error(err);
    });
}

function closeScanner() {
    const container = document.getElementById('scanner-container');
    container.classList.add('hidden');

    if (codeReader) {
        codeReader.reset();
    }
}

function beep() {
    const audio = new Audio("https://www.soundjay.com/buttons/sounds/beep-07.mp3");
    audio.play();
}

document.getElementById('barcode').addEventListener('input', function () {
    fetchProductByBarcode(this.value);
});

function fetchProductByBarcode(barcode) {
    if (!barcode) return;

    fetch(`/admin/products/barcode/${barcode}`)
        .then(res => res.ok ? res.json() : null)
        .then(data => {
            if (data) {
                document.getElementById('name').value = data.name;
                document.getElementById('price').value = data.price;
                document.getElementById('purchase_price').value = data.purchase_price;
            } else {
                document.getElementById('name').value = '';
                document.getElementById('price').value = '';
                document.getElementById('purchase_price').value = '';
            }
        })
        .catch(() => {
            document.getElementById('name').value = '';
            document.getElementById('price').value = '';
            document.getElementById('purchase_price').value = '';
        });
}
</script>
@endsection
