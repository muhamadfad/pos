@extends('layouts.admin')

@section('title', 'Tambah Produksi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12" x-data="productionForm()">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.central-productions.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 mb-2 transition-colors">
                <span class="iconify mr-1" data-icon="lucide:arrow-left"></span>
                Kembali ke Daftar Produksi
            </a>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Olah Bahan Baku</h1>
            <p class="text-slate-500 mt-1">Ubah bahan mentah menjadi bahan olahan dengan kalkulasi yield otomatis.</p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="iconify text-rose-500 mt-0.5 text-lg" data-icon="lucide:alert-circle"></span>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-rose-800">Terdapat kesalahan saat menyimpan:</h3>
                    <ul class="mt-2 text-sm text-rose-700 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.central-productions.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- SECTION 1: Hasil Produksi -->
        <div class="bg-white rounded-3xl shadow-sm shadow-slate-200/50 border border-slate-200 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-2 h-full bg-indigo-500"></div>
            <div class="p-6 sm:p-8 pl-8 sm:pl-10">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                        <span class="text-xl font-black">1</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Informasi Hasil Produksi</h2>
                        <p class="text-sm text-slate-500">Tentukan barang jadi yang dihasilkan dari proses memasak.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Produksi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="iconify text-slate-400" data-icon="lucide:calendar-days"></span>
                            </div>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                                   class="w-full pl-10 bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 py-3 transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Referensi (Opsional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="iconify text-slate-400" data-icon="lucide:hash"></span>
                            </div>
                            <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="PRD-XXXX"
                                   class="w-full pl-10 bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 py-3 transition-colors">
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 mt-4">
                        <div class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-indigo-900 mb-2">Pilih Barang Jadi <span class="text-rose-500">*</span></label>
                                    <select name="result_ingredient_id" required class="w-full bg-white border border-indigo-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-4 py-3 shadow-sm font-medium appearance-none">
                                        <option value="">-- Pilih Barang Jadi --</option>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" {{ old('result_ingredient_id') == $ing->id ? 'selected' : '' }}>
                                                {{ $ing->name }} (Stok saat ini: {{ floatval($ing->current_stock) }} {{ $ing->unit->name ?? 'kg' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-indigo-900 mb-2">Berat/Jumlah Aktual (Yield) <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="result_quantity" value="{{ old('result_quantity') }}" required placeholder="Contoh: 7.5"
                                               class="w-full bg-white border border-indigo-200 text-slate-800 text-lg font-bold rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 pl-4 pr-12 py-2.5 shadow-sm">
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-bold">
                                            Kg
                                        </div>
                                    </div>
                                    <p class="text-xs text-indigo-600 mt-2 flex items-start leading-snug">
                                        <span class="iconify mr-1.5 mt-0.5 shrink-0" data-icon="lucide:info" data-width="14"></span>
                                        Masukkan berat BERSIH yang didapatkan SETELAH daging/bahan selesai dimasak.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Bahan Baku Dipakai -->
        <div class="bg-white rounded-3xl shadow-sm shadow-slate-200/50 border border-slate-200 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-2 h-full bg-amber-500"></div>
            <div class="p-6 sm:p-8 pl-8 sm:pl-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-inner">
                            <span class="text-xl font-black">2</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Bahan Mentah yang Dipakai</h2>
                            <p class="text-sm text-slate-500">Masukkan bahan kotor yang digunakan sebelum menyusut.</p>
                        </div>
                    </div>
                    <button type="button" @click="addItem" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 text-white font-semibold text-sm rounded-xl hover:bg-slate-700 transition-colors shadow-md">
                        <span class="iconify mr-2" data-icon="lucide:plus" data-width="16"></span>
                        Tambah Bahan Mentah
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-col sm:flex-row gap-4 p-5 border border-slate-200 rounded-2xl bg-slate-50/50 relative group hover:border-amber-200 hover:bg-amber-50/30 transition-all">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Pilih Bahan Mentah</label>
                                <select x-model="item.ingredient_id" :name="'items['+index+'][ingredient_id]'" required class="w-full bg-white border border-slate-200 text-slate-800 font-medium text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 px-4 py-3 appearance-none shadow-sm" x-init="$nextTick(() => { $($el).select2({width: '100%'}); $($el).on('change', function(){ item.ingredient_id = $(this).val(); }); $watch('item.ingredient_id', val => { if($($el).val() !== val) $($el).val(val).trigger('change.select2'); }) })">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}">{{ $ing->name }} (Modal: Rp {{ number_format($ing->unit_cost,0,',','.') }}/{{ $ing->unit->name ?? 'kg' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end gap-3 w-full sm:w-auto">
                                <div class="w-full sm:w-48">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Berat/Jml Kotor</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" x-model="item.quantity" :name="'items['+index+'][quantity]'" required min="0.1" placeholder="0.0"
                                               class="w-full bg-white border border-slate-200 text-slate-800 font-bold text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 pl-4 pr-12 py-3 shadow-sm text-right">
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-medium text-sm">
                                            Kg
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="w-12 h-[46px] bg-rose-50 border border-rose-200 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm shrink-0" title="Hapus Bahan">
                                    <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-100/50 flex gap-3 items-start">
                    <span class="iconify text-amber-500 mt-0.5 text-lg shrink-0" data-icon="lucide:info"></span>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        Jika stok daging mentah habis/kosong sebelum Anda memasak, sistem <strong>akan otomatis membiarkannya menjadi minus</strong>. Anda dapat melunasinya kapan-kapan melalui menu Pembelian Pusat tanpa mengganggu operasional masak dapur!
                    </p>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Catatan & Submit -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100">
                <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                    <span class="iconify text-slate-400" data-icon="lucide:message-square"></span>
                    Catatan Produksi (Opsional)
                </label>
                <textarea name="notes" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-4 transition-colors placeholder:text-slate-400" placeholder="Ketikkan catatan khusus, misal: Daging susut banyak karena kadar air tinggi..."></textarea>
            </div>
            <div class="px-6 py-5 bg-slate-50/50 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 sm:gap-4">
                <a href="{{ route('admin.central-productions.index') }}" class="w-full sm:w-auto text-center px-6 py-3 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
                    <span class="iconify" data-icon="lucide:save"></span>
                    Simpan Produksi
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.searchable-select').select2({ width: '100%' });
    });
    
    function productionForm() {
        return {
            items: [
                { ingredient_id: '', quantity: '' }
            ],
            addItem() {
                this.items.push({ ingredient_id: '', quantity: '' });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        }
    }
</script>
@endpush
@endsection
