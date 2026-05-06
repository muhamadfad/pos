@extends('layouts.admin')

@section('title', 'Kategori Produk')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div x-data="categoryPage()" class="p-2 lg:p-4 max-w-[1600px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-lg text-indigo-600 shadow-sm">
                <span class="iconify text-xl" data-icon="lucide:layers"></span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kategori Produk</h1>
                <p class="text-sm text-slate-500 font-medium">Kelola pengelompokan inventaris untuk struktur yang rapi.</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:block text-right pr-4 border-r border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Record</p>
                <p class="text-lg font-bold text-slate-800 leading-none">{{ $total }}</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus"></span>
                Tambah Kategori
            </a>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:flex-1 relative group">
            <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors" data-icon="lucide:search"></span>
            <input type="text" 
                x-model="search" 
                @input.debounce.400ms="fetchData()" 
                placeholder="Cari nama kategori..."
                class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-700 placeholder:text-slate-400 font-medium">
            
            <button x-show="search" @click="search=''; fetchData()" 
                class="absolute inset-y-0 right-3 flex items-center text-slate-300 hover:text-rose-500 transition-colors">
                <span class="iconify text-lg" data-icon="lucide:x"></span>
            </button>
        </div>

        <div class="flex shrink-0">
            <div class="bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 flex items-center gap-2.5">
                <div class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest">Live Database</span>
            </div>
        </div>
    </div>

    <div class="relative min-h-[400px]">
        <div x-show="loading" 
             x-transition:enter="transition opacity-0" 
             x-transition:enter-end="opacity-100"
             class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-20 flex items-center justify-center rounded-xl" style="display: none;">
            <div class="flex flex-col items-center gap-2">
                <div class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Memuat...</span>
            </div>
        </div>

        <div id="tableContent" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden transition-all">
            @include('admin.categories.partials.table')
        </div>
    </div>

</div>

<script>
    function categoryPage() {
        return {
            search: '',
            loading: false,
            fetchData(pageUrl = null) {
                this.loading = true;
                let url = pageUrl ?? `?search=${encodeURIComponent(this.search)}`;
                
                fetch(url, { 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('tableContent').innerHTML = html;
                    if (window.Iconify) window.Iconify.scan();
                    this.loading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loading = false;
                });
            }
        }
    }
</script>

<style>
    /* Sinkronisasi scrollbar dengan produk */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    #tableContent { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection