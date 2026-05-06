@extends('layouts.admin')

@section('title', isset($isEdit) && $isEdit ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="max-w-[1100px] mx-auto space-y-6 pb-20" style="font-family: 'Inter', sans-serif;">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" 
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition-all shadow-sm">
                <span class="iconify text-xl" data-icon="lucide:arrow-left"></span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                    {{ isset($isEdit) && $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </h1>
                <p class="text-sm text-slate-500 font-medium">Lengkapi detail informasi produk inventaris Anda.</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl flex gap-3">
        <span class="iconify text-rose-500 text-xl shrink-0" data-icon="lucide:alert-circle"></span>
        <div class="text-sm">
            <h4 class="font-bold text-rose-800">Ada kendala pada input Anda:</h4>
            <ul class="text-rose-700/80 list-disc list-inside mt-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ isset($isEdit) && $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" 
          method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @if(isset($isEdit) && $isEdit) @method('PUT') @endif

        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Produk</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Barcode / SKU</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1 group">
                                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" data-icon="lucide:barcode"></span>
                                <input type="text" id="barcode" name="barcode" required
                                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 placeholder:text-slate-400 font-mono"
                                    placeholder="Scan atau ketik barcode..." value="{{ old('barcode', $product->barcode ?? '') }}">
                            </div>
                            <button type="button" onclick="openScanner()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition shadow-sm active:scale-95">
                                <span class="iconify text-base" data-icon="lucide:camera"></span>
                                Scan
                            </button>
                        </div>
                        
                        <div id="scanner-container" class="hidden mt-4 rounded-xl overflow-hidden border border-slate-200 bg-black relative shadow-lg">
                            <video id="scanner" class="w-full h-48 object-cover"></video>
                            <button type="button" onclick="closeScanner()"
                                class="absolute top-2 right-2 p-1.5 bg-white/20 hover:bg-white/40 text-white backdrop-blur rounded-md transition">
                                <span class="iconify" data-icon="lucide:x"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama Produk</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2.5 text-sm font-medium bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-800 placeholder:text-slate-400"
                            placeholder="Contoh: Oli Mesin Shell Helix 1L" value="{{ old('name', $product->name ?? '') }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Kategori</label>
                            <select name="category_id" required
                                class="w-full px-4 py-2.5 text-sm font-medium bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 cursor-pointer appearance-none">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Satuan</label>
                            <select name="unit_id"
                                class="w-full px-4 py-2.5 text-sm font-medium bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 cursor-pointer appearance-none">
                                <option value="">Pilih Satuan</option>
                                @foreach ($units ?? [] as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->abbreviation }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-semibold text-slate-800 text-center">Finansial & Stok</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Harga Beli (Modal)</label>
                        <div class="relative group">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" id="purchase_price" name="purchase_price" required
                                class="w-full pl-10 pr-4 py-2.5 text-sm font-semibold bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-rose-500/5 focus:border-rose-400 transition-all text-slate-800"
                                placeholder="0" value="{{ old('purchase_price', $product->purchase_price ?? '') }}">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Harga Jual</label>
                        <div class="relative group">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" id="selling_price" name="selling_price" required
                                class="w-full pl-10 pr-4 py-2.5 text-sm font-semibold bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-400 transition-all text-slate-800"
                                placeholder="0" value="{{ old('selling_price', $product->selling_price ?? '') }}">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Stok Awal</label>
                        <div class="relative group">
                            <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg" data-icon="lucide:layers"></span>
                            <input type="number" id="stock" name="stock" required
                                class="w-full pl-10 pr-4 py-2.5 text-sm font-semibold bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-400 transition-all text-slate-800"
                                placeholder="0" value="{{ old('stock', $product->stock ?? '') }}">
                        </div>
                    </div>

                    <div id="marginInfo" class="hidden p-4 rounded-lg bg-indigo-50/50 border border-indigo-100 space-y-1">
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-indigo-500 uppercase tracking-wider">
                            <span class="iconify" data-icon="lucide:trending-up"></span>
                            Estimated Margin
                        </div>
                        <span id="marginText" class="text-sm font-bold text-indigo-700 tracking-tight"></span>
                    </div>

                    <div class="pt-4 space-y-3">
                        <button type="submit"
                            class="w-full py-3 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 shadow-sm hover:shadow-indigo-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span class="iconify text-lg" data-icon="{{ isset($isEdit) && $isEdit ? 'lucide:save' : 'lucide:check-circle' }}"></span>
                            {{ isset($isEdit) && $isEdit ? 'Simpan Perubahan' : 'Terbitkan Produk' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="w-full py-3 bg-white text-slate-500 text-sm font-semibold rounded-lg border border-slate-200 hover:bg-slate-50 transition-all flex items-center justify-center">
                            Batalkan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
    // Logika JS tetap sama karena sudah fungsional, hanya UI yang kita kurangi beratnya.
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
                document.getElementById('barcode').value = result.text;
                closeScanner();
            }
        }).catch(() => closeScanner());
    }

    function closeScanner() {
        document.getElementById('scanner-container').classList.add('hidden');
        if (codeReader) codeReader.reset();
    }

    function updateMargin() {
        const buy = parseFloat(document.getElementById('purchase_price').value) || 0;
        const sell = parseFloat(document.getElementById('selling_price').value) || 0;
        const info = document.getElementById('marginInfo');
        const text = document.getElementById('marginText');

        if (buy > 0 && sell > 0) {
            const margin = sell - buy;
            const pct = ((margin / buy) * 100).toFixed(1);
            text.textContent = `Rp ${margin.toLocaleString('id-ID')} (${pct}%)`;
            info.classList.remove('hidden');
        } else {
            info.classList.add('hidden');
        }
    }

    document.getElementById('purchase_price').addEventListener('input', updateMargin);
    document.getElementById('selling_price').addEventListener('input', updateMargin);
    window.addEventListener('load', updateMargin);
</script>
@endsection