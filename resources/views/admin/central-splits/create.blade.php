@extends('layouts.admin')
@section('title', 'Pemisahan Bahan Baku (Pusat)')

@section('content')
<div x-data="returnForm()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pemisahan Bahan Baku (Pusat)</h1>
            <p class="text-sm text-slate-500 mt-1">Pisahkan satu bahan baku (misal: Daging Campur) menjadi beberapa item berbeda (misal: Hati, Paru) di Gudang Pusat.</p>
        </div>
        <a href="{{ route('admin.central-splits.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <span class="iconify text-red-500 text-xl shrink-0" data-icon="mdi:alert-circle"></span>
        <ul class="text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.central-splits.store') }}" method="POST" @submit="handleFormSubmit" novalidate>
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <h3 class="font-bold text-slate-700 tracking-tight mb-4 border-b border-slate-100 pb-2">1. Bahan Baku Sumber</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Pemisahan</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold" required>
                </div>
                
                <!-- Source Item -->
                <div class="md:col-span-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bahan Baku Yang Akan Dipisah</label>
                    <div class="relative" x-data="{
                        open: false,
                        search: '',
                        get filteredIngredients() {
                            if (!this.search) return window.__ingredients;
                            return window.__ingredients.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }">
                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="source_ingredient_id" x-model="source_ingredient_id">
                        
                        <div class="relative" @click.away="open = false">
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-slate-400">
                                    <span class="iconify" data-icon="mdi:magnify"></span>
                                </span>
                                <input type="text"
                                    x-model="search"
                                    @focus="open = true"
                                    @click="open = true"
                                    class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-10 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-400"
                                    placeholder="Cari bahan baku (Campur)..."
                                    autocomplete="off">
                                
                                <button type="button" class="absolute right-2 text-slate-400 hover:text-slate-600 p-1" @click="open = !open">
                                    <span class="iconify transition-transform duration-200" :class="open ? 'rotate-180' : ''" data-icon="mdi:chevron-down"></span>
                                </button>
                            </div>

                            <div x-show="open" 
                                x-transition.opacity
                                class="absolute z-[60] mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto py-1"
                                style="display: none;">
                                
                                <div x-show="false" class="px-4 py-3 text-sm text-slate-500 text-center">
                                    Pilih cabang asal terlebih dahulu
                                </div>
                                
                                <div x-show="filteredIngredients.length === 0" class="px-4 py-3 text-sm text-slate-500 text-center">
                                    Tidak ditemukan - "<span x-text="search"></span>"
                                </div>

                                <template x-for="i in filteredIngredients" :key="i.id">
                                    <button type="button" @click="source_ingredient_id = i.id; source_stock_available = i.stock; source_unit_name = i.unit_name; search = i.name; open = false;"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition-colors flex items-center justify-between group">
                                        <div>
                                            <div class="font-medium text-slate-700 group-hover:text-indigo-600 transition-colors" x-text="i.name"></div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-[11px] text-slate-400">Stok: <span x-text="i.stock + ' ' + i.unit_name"></span></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jumlah Yang Dipisah</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="source_quantity" x-model="source_quantity" min="0.1" step="any" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold" required>
                        <span class="text-sm font-semibold text-slate-500" x-text="source_unit_name"></span>
                    </div>
                    
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                    <input type="text" name="notes" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold" placeholder="Contoh: Pengembalian daging campur sisa">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-visible mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="iconify text-slate-400" data-icon="mdi:call-split"></span>
                    <div>
                        <h3 class="font-bold text-slate-700 tracking-tight">2. Hasil Pemisahan (Di Gudang Pusat)</h3>
                        <p class="text-[11px] text-slate-500">Barang ini akan ditambahkan ke stok Gudang Pusat.</p>
                    </div>
                </div>
                <button type="button" @click="addRow()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-[12px] font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:plus"></span>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-visible font-sans">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left w-2/5">Bahan Baku (Pusat)</th>
                            <th class="px-6 py-3 text-center">Jumlah Diterima</th>
                            <th class="px-6 py-3 text-left">Catatan</th>
                            <th class="px-6 py-3 text-center w-12">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="relative" x-data="{
                                        open: false,
                                        search: '',
                                        get filtered() {
                                            if (!this.search) return window.__ingredients;
                                            return window.__ingredients.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                                        }
                                    }">
                                        <input type="hidden" :name="'items[' + index + '][ingredient_id]'" x-model="item.ingredient_id">
                                        <div class="relative" @click.away="open = false">
                                            <input type="text" x-model="search" @focus="open = true" @click="open = true" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" placeholder="Cari bahan baku pusat...">
                                            <div x-show="open" class="absolute z-[60] mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto py-1" style="display: none;">
                                                <div x-show="filtered.length === 0" class="px-4 py-2 text-xs text-slate-500">Tidak ditemukan</div>
                                                <template x-for="i in filtered" :key="i.id">
                                                    <button type="button" @click="item.ingredient_id = i.id; item.unit_name = i.unit_name; search = i.name; open = false;" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50" x-text="i.name"></button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <input type="number" :name="'items[' + index + '][quantity]'" min="0.1" step="any" class="w-32 bg-white border border-slate-200 rounded-xl px-2 py-2 text-sm text-center focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold" x-model="item.quantity">
                                        <span class="text-xs text-slate-400" x-text="item.unit_name || ''"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" :name="'items[' + index + '][notes]'" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500" x-model="item.notes" placeholder="Cth: Kondisi bagus">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeRow(index)" class="text-slate-300 hover:text-red-500 p-1">
                                        <span class="iconify text-xl" data-icon="mdi:trash-can-outline"></span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.central-splits.index') }}"
                class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-all">
                Batalkan
            </a>
            <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2 disabled:bg-slate-400 disabled:cursor-not-allowed"
                :disabled="!isValid()">
                <span class="iconify text-xl" data-icon="mdi:send-check-outline"></span>
                Proses Pemisahan
            </button>
        </div>
    </form>
</div>

<script>
    window.__ingredients = {!! json_encode($ingredients->map(fn($p) => [
        'id'             => $p->id,
        'name'           => $p->name,
        'dummy'          => 0,
        'stock'          => (float) $p->current_stock,
        'unit_name'      => $p->unit->name ?? 'pcs',
    ])->values()) !!};

    function returnForm() {
        return {
            init() {
                
            },
            
            // Source Item
            source_ingredient_id: '',
            source_quantity: 1,
            source_stock_available: 0,
            source_unit_name: '',
            
            

            // Target Items
            items: [
                { ingredient_id: '', quantity: 1, unit_name: '', notes: '', _search: '', _open: false, _dropPos: null }
            ],

            addRow() {
                this.items.push({ ingredient_id: '', quantity: 1, unit_name: '', notes: '', _search: '', _open: false, _dropPos: null });
            },

            removeRow(index) {
                this.items.splice(index, 1);
            },

            isValid() {
                const hasSource = this.source_ingredient_id && this.source_quantity > 0 ;
                const validTargets = this.items.filter(item => item.ingredient_id && item.quantity > 0);
                return hasSource && validTargets.length > 0;
            },

            handleFormSubmit(e) {
                if (!this.isValid()) {
                    e.preventDefault();
                    alert('Harap lengkapi Bahan Baku Sumber (stok cukup), dan minimal 1 Hasil Pemisahan!');
                }
            }
        }
    }
</script>
@endsection