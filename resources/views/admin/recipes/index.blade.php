@extends('layouts.admin')

@section('title', 'Manajemen Resep Menu')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div x-data="recipeTable()" 
     x-init="init()"
     class="p-2 lg:p-4 max-w-[1600px] mx-auto space-y-6" 
     style="font-family: 'Inter', sans-serif;">

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <span>Inventory</span>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">Recipes</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Manajemen Resep Menu</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola komposisi bahan baku untuk setiap menu</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="fetchData()" 
                class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all active:scale-95 shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:refresh-cw" :class="loading ? 'animate-spin' : ''"></span>
            </button>
            <a href="{{ route('admin.recipes.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus"></span>
                Tambah Resep
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Resep</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_recipes'] ?? count($products)) }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-indigo-600" data-icon="lucide:book-open"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Menu</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($products->count()) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-blue-600" data-icon="lucide:utensils"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Bahan</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalIngredients ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-emerald-600" data-icon="lucide:package"></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Rata-rata Bahan</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($avgIngredientsPerRecipe ?? 0, 1) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <span class="iconify text-xl text-purple-600" data-icon="lucide:bar-chart-2"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
        
        <div class="lg:col-span-8 relative group">
            <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg" data-icon="lucide:search"></span>
            <input type="text" x-model="search" @input.debounce.500ms="fetchData"
                placeholder="Cari Nama Menu atau Kategori..."
                class="w-full pl-11 pr-4 py-2.5 text-sm bg-transparent border-none focus:ring-0 text-slate-700 placeholder:text-slate-400">
        </div>

        <div class="lg:col-span-4 flex flex-wrap lg:justify-end items-center gap-3 pr-2">
            <select x-model="per_page" @change="fetchData"
                class="text-[11px] font-bold text-slate-500 border-none bg-transparent focus:ring-0 cursor-pointer uppercase tracking-tight">
                <option value="10">10 Baris</option>
                <option value="25">25 Baris</option>
                <option value="50">50 Baris</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden relative min-h-[400px]">
        <div x-show="loading" x-transition.opacity
            class="absolute inset-0 bg-white/80 backdrop-blur-[1px] z-20 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-8 h-8 border-[3px] border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <span class="text-xs font-medium text-slate-500 tracking-wide">Memuat data...</span>
            </div>
        </div>

        <div id="tableContent" class="overflow-x-auto">
            @include('admin.recipes.partials.table')
        </div>
    </div>
</div>

<script>
    function recipeTable() {
        return {
            search: '',
            per_page: 10,
            loading: false,

            init() {
                const urlParams = new URLSearchParams(window.location.search);
                this.search = urlParams.get('search') || '';
                this.per_page = urlParams.get('per_page') || 10;
            },

            fetchData() {
                this.loading = true;
                
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.search);
                url.searchParams.set('per_page', this.per_page);
                window.history.pushState({}, '', url);
                
                const params = new URLSearchParams({
                    search: this.search,
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
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
            },

            fetchPage(url) {
                if (!url) return;
                this.loading = true;
                
                const currentUrl = new URL(url);
                currentUrl.searchParams.set('search', this.search);
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
@endsection