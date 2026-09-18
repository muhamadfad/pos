@extends('layouts.admin')

@section('title', 'Manajemen Bahan Baku')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div x-data="ingredientTable()" 
     x-init="init()"
     class="p-2 lg:p-4 max-w-[1600px] mx-auto space-y-6" 
     style="font-family: 'Inter', sans-serif;">

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <span>Inventory</span>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">Ingredients</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Manajemen Bahan Baku</h1>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="fetchData()" 
                class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all active:scale-95 shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:refresh-cw" :class="loading ? 'animate-spin' : ''"></span>
            </button>
            <a href="{{ route('admin.ingredients.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus"></span>
                Tambah Bahan
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Bahan</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_ingredients']) }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-indigo-600" data-icon="lucide:package"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Stok Menipis</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['low_stock']) }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-amber-600" data-icon="lucide:alert-triangle"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Stok Habis</p>
                    <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($stats['out_of_stock']) }}</p>
                </div>
                <div class="w-10 h-10 bg-rose-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-rose-600" data-icon="lucide:package-x"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Nilai Stok</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-emerald-600" data-icon="lucide:wallet"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
        
        <div class="lg:col-span-6 relative group">
            <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg" data-icon="lucide:search"></span>
            <input type="text" x-model="search" @input.debounce.500ms="fetchData"
                placeholder="Cari Nama Bahan Baku..."
                class="w-full pl-11 pr-4 py-2.5 text-sm bg-transparent border-none focus:ring-0 text-slate-700 placeholder:text-slate-400">
        </div>

        <div class="lg:col-span-6 flex flex-wrap lg:justify-end items-center gap-3 pr-2">
            <select x-model="branch_id" @change="fetchData"
                class="text-sm font-medium text-slate-600 bg-slate-50 border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer py-2 pl-3 pr-10">
                <option value="">Semua Cabang</option>
                @php
                    $uniqueBranches = $branches->unique('id');
                @endphp
                @foreach($uniqueBranches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>

            <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

            <select x-model="per_page" @change="fetchData"
                class="text-[11px] font-bold text-slate-500 border-none bg-transparent focus:ring-0 cursor-pointer uppercase tracking-tight">
                <option value="10">10 Baris</option>
                <option value="25">25 Baris</option>
                <option value="50">50 Baris</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden relative min-h-[400px]">
        <div x-show="loading" x-transition.opacity
            class="absolute inset-0 bg-white/80 backdrop-blur-[1px] z-20 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-8 h-8 border-[3px] border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <span class="text-xs font-medium text-slate-500 tracking-wide">Memuat data...</span>
            </div>
        </div>

        <div id="tableContent" class="overflow-x-auto">
            @include('admin.ingredients.partials.table')
        </div>
    </div>
</div>

<script>
    function ingredientTable() {
        return {
            search: '',
            branch_id: '',
            per_page: 10,
            loading: false,

            init() {
                // Ambil nilai dari URL parameter saat pertama kali load
                const urlParams = new URLSearchParams(window.location.search);
                this.search = urlParams.get('search') || '';
                this.branch_id = urlParams.get('branch_id') || '';
                this.per_page = urlParams.get('per_page') || 10;
            },

            fetchData() {
                this.loading = true;
                
                // Update URL tanpa reload
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.search);
                url.searchParams.set('branch_id', this.branch_id);
                url.searchParams.set('per_page', this.per_page);
                window.history.pushState({}, '', url);
                
                const params = new URLSearchParams({
                    search: this.search,
                    branch_id: this.branch_id,
                    per_page: this.per_page
                });
                
                fetch(`?${params.toString()}`, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/xhtml+xml'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    // Ekstrak hanya bagian tableContent dari response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableContent = doc.getElementById('tableContent');
                    
                    if (newTableContent) {
                        document.getElementById('tableContent').innerHTML = newTableContent.innerHTML;
                    } else {
                        // Fallback: update seluruh konten
                        document.getElementById('tableContent').innerHTML = html;
                    }
                    
                    if (window.Iconify) window.Iconify.scan();
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
            },

            fetchPage(url) {
                if (!url) return;
                this.loading = true;
                
                // Update URL params
                const currentUrl = new URL(url);
                currentUrl.searchParams.set('search', this.search);
                currentUrl.searchParams.set('branch_id', this.branch_id);
                currentUrl.searchParams.set('per_page', this.per_page);

                fetch(currentUrl.toString(), {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/xhtml+xml'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableContent = doc.getElementById('tableContent');
                    
                    if (newTableContent) {
                        document.getElementById('tableContent').innerHTML = newTableContent.innerHTML;
                    } else {
                        document.getElementById('tableContent').innerHTML = html;
                    }
                    
                    if (window.Iconify) window.Iconify.scan();
                    this.loading = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
            }
        }
    }
</script>

<style>
    input:focus { outline: none !important; }
    #tableContent::-webkit-scrollbar { height: 6px; }
    #tableContent::-webkit-scrollbar-track { background: #f1f1f1; }
    #tableContent::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    #tableContent::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
<!-- Modal Quick Update Stock -->
<div x-data="{ 
    open: false, 
    id: '', 
    name: '', 
    current: 0,
    unit: '',
    url: ''
}" 
@open-stock-modal.window="
    id = $event.detail.id;
    name = $event.detail.name;
    current = $event.detail.current;
    unit = $event.detail.unit;
    url = '{{ route('admin.ingredients.index') }}/' + id + '/quick-update-stock';
    open = true;
    setTimeout(() => $refs.stockInput.focus(), 100);
"
class="relative z-50">
    <div x-show="open" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" 
             x-transition.enter="transition ease-out duration-300"
             x-transition.enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition.enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition.leave="transition ease-in duration-200"
             x-transition.leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition.leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-slate-100 z-10">
            
            <div class="px-6 py-4 border-b border-slate-100 bg-amber-50/50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm">
                        <span class="iconify text-xl" data-icon="lucide:calculator"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Opname Stok Cepat</h3>
                        <p class="text-[11px] text-slate-500 font-medium" x-text="name"></p>
                    </div>
                </div>
                <button @click="open = false" class="text-slate-400 hover:text-rose-500 transition-colors p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                    <span class="iconify text-xl" data-icon="mdi:close"></span>
                </button>
            </div>

            <form :action="url" method="POST">
                @csrf
                <div class="p-6">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Total Stok Nyata (Saat Ini)</label>
                    <div class="relative flex items-center">
                        <input type="number" name="current_stock" x-model="current" x-ref="stockInput" min="0" step="any" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-lg font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-center">
                        <span class="absolute right-4 text-slate-400 font-medium" x-text="unit"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3 text-center">Masukkan angka sesuai jumlah fisik di gudang.</p>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-xl font-bold hover:bg-amber-700 transition-all text-sm shadow-md shadow-amber-200 flex items-center gap-2">
                        <span class="iconify" data-icon="lucide:check"></span> Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection