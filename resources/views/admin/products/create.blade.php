@extends('layouts.admin')

@section('title', isset($isEdit) && $isEdit ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.products.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                {{ isset($isEdit) && $isEdit ? 'Edit Produk' : 'Tambah Produk' }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ isset($isEdit) && $isEdit ? 'Perbarui informasi produk yang ada' : 'Isi detail produk baru' }}
            </p>
        </div>
    </div>

    @if ($errors->any())
    <div class="flex gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <span class="iconify shrink-0 text-red-500 mt-0.5" data-icon="mdi:alert-circle-outline" data-width="18"></span>
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($isEdit) && $isEdit
            ? route('admin.products.update', $product->id)
            : route('admin.products.store') }}" method="POST" class="space-y-5">
        @csrf
        @if(isset($isEdit) && $isEdit)
        @method('PUT')
        @endif

        <!-- Card: Identitas Produk -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="iconify text-indigo-500" data-icon="mdi:barcode"></span>
                <h3 class="text-sm font-semibold text-slate-700">Identitas Produk</h3>
            </div>
            <div class="p-6 space-y-5">

                <!-- Barcode -->
                <div>
                    <label for="barcode" class="block text-sm font-semibold text-slate-700 mb-1.5">Barcode</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span
                                class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                                data-icon="mdi:barcode-scan" data-width="18"></span>
                            <input type="text" id="barcode" name="barcode"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Scan atau ketik barcode..."
                                value="{{ old('barcode', $product->barcode ?? '') }}" required>
                        </div>
                        <button type="button" onclick="openScanner()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-700 transition shrink-0">
                            <span class="iconify" data-icon="mdi:camera"></span>
                            Scan
                        </button>
                    </div>
                </div>

                <!-- Scanner -->
                <div id="scanner-container"
                    class="hidden rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                    <video id="scanner" class="w-full"></video>
                    <div class="p-3">
                        <button type="button" onclick="closeScanner()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                            <span class="iconify" data-icon="mdi:close-circle-outline"></span>
                            Tutup Kamera
                        </button>
                    </div>
                </div>

                <!-- Nama Produk -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Produk</label>
                    <input type="text" id="name" name="name"
                        class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        placeholder="Masukkan nama produk..." value="{{ old('name', $product->name ?? '') }}" required>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
                    <div class="relative">
                        <span
                            class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            data-icon="mdi:shape-outline" data-width="18"></span>
                        <select name="category_id" id="category_id"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none"
                            required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') ==
                                $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        <span
                            class="iconify absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            data-icon="mdi:chevron-down" data-width="18"></span>
                    </div>
                </div>

                <!-- Satuan -->
                <div>
                    <label for="unit_id" class="block text-sm font-semibold text-slate-700 mb-1.5">Satuan</label>
                    <div class="relative">
                        <span
                            class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            data-icon="mdi:ruler" data-width="18"></span>
                        <select name="unit_id" id="unit_id"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($units ?? [] as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id ?? '') == $unit->id ?
                                'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->abbreviation }})
                            </option>
                            @endforeach
                        </select>
                        <span
                            class="iconify absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            data-icon="mdi:chevron-down" data-width="18"></span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Card: Harga & Stok -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="iconify text-indigo-500" data-icon="mdi:tag-outline"></span>
                <h3 class="text-sm font-semibold text-slate-700">Harga & Stok</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <!-- Harga Beli -->
                    <div>
                        <label for="purchase_price" class="block text-sm font-semibold text-slate-700 mb-1.5">Harga
                            Beli</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium pointer-events-none">Rp</span>
                            <input type="number" id="purchase_price" name="purchase_price"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="0" value="{{ old('purchase_price', $product->purchase_price ?? '') }}"
                                required>
                        </div>
                    </div>

                    <!-- Harga Jual -->
                    <div>
                        <label for="selling_price" class="block text-sm font-semibold text-slate-700 mb-1.5">Harga
                            Jual</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium pointer-events-none">Rp</span>
                            <input type="number" id="selling_price" name="selling_price"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="0" value="{{ old('selling_price', $product->selling_price ?? '') }}"
                                required>
                        </div>
                    </div>

                    <!-- Stok -->
                    <div>
                        <label for="stock" class="block text-sm font-semibold text-slate-700 mb-1.5">Stok Awal</label>
                        <div class="relative">
                            <span
                                class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                                data-icon="mdi:package-variant" data-width="18"></span>
                            <input type="number" id="stock" name="stock"
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="0" value="{{ old('stock', $product->stock ?? '') }}" required>
                        </div>
                    </div>

                </div>

                <!-- Margin Info (auto-hitung) -->
                <div id="marginInfo"
                    class="hidden mt-4 p-3 bg-indigo-50 border border-indigo-100 rounded-xl text-sm text-indigo-700 flex items-center gap-2">
                    <span class="iconify shrink-0" data-icon="mdi:chart-line" data-width="16"></span>
                    <span id="marginText"></span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pb-6">
            <a href="{{ route('admin.products.index') }}"
                class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition shadow-md shadow-indigo-100">
                <span class="iconify"
                    data-icon="{{ isset($isEdit) && $isEdit ? 'mdi:content-save-edit-outline' : 'mdi:plus-circle-outline' }}"></span>
                {{ isset($isEdit) && $isEdit ? 'Simpan Perubahan' : 'Tambah Produk' }}
            </button>
        </div>

    </form>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
    let codeReader;
let scanning = false;

function openScanner() {
    document.getElementById('scanner-container').classList.remove('hidden');
    codeReader = new ZXing.BrowserMultiFormatReader();
    scanning = true;
    codeReader.decodeFromVideoDevice(null, 'scanner', (result, err) => {
        if (result && scanning) {
            scanning = false;
            const barcode = result.text;
            document.getElementById('barcode').value = barcode;
            closeScanner();
            fetchProductByBarcode(barcode);
        }
    }).catch(() => closeScanner());
}

function closeScanner() {
    document.getElementById('scanner-container').classList.add('hidden');
    if (codeReader) codeReader.reset();
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
                document.getElementById('selling_price').value = data.selling_price;
                document.getElementById('purchase_price').value = data.purchase_price;
                updateMargin();
            }
        });
}

function updateMargin() {
    const buy = parseFloat(document.getElementById('purchase_price').value) || 0;
    const sell = parseFloat(document.getElementById('selling_price').value) || 0;
    const info = document.getElementById('marginInfo');
    const text = document.getElementById('marginText');

    if (buy > 0 && sell > 0) {
        const margin = sell - buy;
        const pct = ((margin / buy) * 100).toFixed(1);
        text.textContent = `Margin: Rp ${margin.toLocaleString('id-ID')} (${pct}%)`;
        info.classList.remove('hidden');
    } else {
        info.classList.add('hidden');
    }
}

document.getElementById('purchase_price').addEventListener('input', updateMargin);
document.getElementById('selling_price').addEventListener('input', updateMargin);

// init margin saat edit
document.addEventListener('DOMContentLoaded', updateMargin);
</script>
@endsection
