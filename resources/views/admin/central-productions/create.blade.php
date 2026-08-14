@extends('layouts.admin')

@section('title', 'Tambah Produksi (Olah Bahan)')

@section('content')
<div class="max-w-4xl mx-auto" x-data="productionForm()">
    
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Olah Bahan Baku</h1>
            <p class="text-slate-500 mt-1 text-sm">Ubah bahan mentah menjadi bahan olahan dengan yield dinamis</p>
        </div>
        <a href="{{ route('admin.central-productions.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium flex items-center transition-colors">
            <span class="iconify mr-1" data-icon="lucide:arrow-left" data-width="16"></span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
        <div class="flex items-start">
            <span class="iconify text-red-500 mt-0.5 mr-3 flex-shrink-0" data-icon="lucide:alert-circle" data-width="20"></span>
            <div>
                <h3 class="text-red-800 font-semibold text-sm mb-1">Terdapat Kesalahan</h3>
                <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.central-productions.store') }}" method="POST" id="productionForm" class="space-y-6">
        @csrf
        
        <!-- Informasi Utama -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-3">1</span>
                    Informasi Hasil Produksi (Barang Jadi)
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Produksi <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                               class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">No. Referensi (Opsional)</label>
                        <input type="text" name="reference_number" placeholder="PRD-XXXX"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                    </div>
                    
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-sm font-medium text-blue-900 mb-2">Pilih Barang Jadi <span class="text-red-500">*</span></label>
                            <select name="result_ingredient_id" required class="w-full bg-white border border-blue-200 text-slate-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                                <option value="">-- Pilih Barang Jadi --</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" data-unit="{{ $ing->unit->name ?? 'kg' }}">{{ $ing->name }} (Stok: {{ $ing->current_stock }} {{ $ing->unit->name ?? 'kg' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-900 mb-2">Berat/Jumlah Aktual (Yield) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" step="0.01" name="result_quantity" required placeholder="0.00"
                                       class="w-full bg-white border border-blue-200 text-slate-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 pl-4 pr-12 py-2.5">
                            </div>
                            <p class="text-xs text-blue-600 mt-1.5 flex items-center">
                                <span class="iconify mr-1" data-icon="lucide:info" data-width="12"></span>
                                Masukkan berat SETELAH dimasak/diolah.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bahan Baku yang Dipakai -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-slate-800 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mr-3">2</span>
                        Bahan Mentah yang Dipakai
                    </h2>
                    <button type="button" @click="addItem" class="inline-flex items-center justify-center px-4 py-2 bg-orange-50 text-orange-600 font-semibold text-sm rounded-lg hover:bg-orange-100 transition-colors">
                        <span class="iconify mr-2" data-icon="lucide:plus" data-width="16"></span>
                        Tambah Bahan Mentah
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-col sm:flex-row gap-4 p-4 border border-slate-100 rounded-xl bg-slate-50 relative group">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="absolute -right-2 -top-2 w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-200">
                                <span class="iconify" data-icon="lucide:x" data-width="14"></span>
                            </button>
                            
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bahan Mentah</label>
                                <select x-model="item.ingredient_id" :name="`items[${index}][ingredient_id]`" required class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 px-3 py-2">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}" data-cost="{{ $ing->unit_cost }}">{{ $ing->name }} (Stok: {{ $ing->current_stock }} {{ $ing->unit->name ?? 'kg' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="w-full sm:w-48">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jumlah Dipakai</label>
                                <input type="number" step="0.01" x-model="item.quantity" :name="`items[${index}][quantity]`" required min="0.1"
                                       class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 px-3 py-2 text-right">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Catatan & Submit -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Produksi</label>
                <textarea name="notes" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 p-4" placeholder="Misal: Daging susut banyak karena kadar air tinggi..."></textarea>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.central-productions.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200">
                    Simpan & Proses Produksi
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
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
