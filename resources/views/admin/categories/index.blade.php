@extends('layouts.admin')

@section('title', 'Kategori Produk')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div x-data="categoryPage()" class="p-2 lg:p-4 max-w-[1600px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-10 h-10 bg-gray-50 border border-gray-200 rounded-md text-black shadow-sm">
                <span class="iconify text-xl" data-icon="lucide:layers"></span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-black tracking-tight">Kategori Produk</h1>
                <p class="text-sm text-gray-500 font-medium">Kelola pengelompokan inventaris untuk struktur yang rapi.</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:block text-right pr-4 border-r border-gray-200">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Record</p>
                <p class="text-base font-semibold text-black leading-none">{{ $total }}</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-black text-white text-sm font-bold rounded-md hover:bg-gray-800 transition-all shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus"></span>
                Tambah Kategori
            </a>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:flex-1 relative group">
            <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-black transition-colors" data-icon="lucide:search"></span>
            <input type="text" 
                x-model="search" 
                @input.debounce.400ms="fetchData()" 
                placeholder="Cari nama kategori..."
                class="w-full pl-10 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all text-slate-700 placeholder:text-gray-500 font-medium">
            
            <button x-show="search" @click="search=''; fetchData()" 
                class="absolute inset-y-0 right-3 flex items-center text-slate-300 hover:text-rose-500 transition-colors">
                <span class="iconify text-lg" data-icon="lucide:x"></span>
            </button>
        </div>

        <div class="flex shrink-0">
            <div class="bg-white border border-gray-200 border border-gray-200 rounded-md px-3 py-2 flex items-center gap-2.5">
                <div class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-md bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-md h-2 w-2 bg-white border border-gray-2000"></span>
                </div>
                <span class="text-[10px] font-bold text-black uppercase tracking-widest">Live Database</span>
            </div>
        </div>
    </div>

    <div class="relative min-h-[400px]">
        <div x-show="loading" 
             x-transition:enter="transition opacity-0" 
             x-transition:enter-end="opacity-100"
             class="absolute inset-0 bg-gray-50/60 backdrop-blur-[1px] z-20 flex items-center justify-center rounded-md" style="display: none;">
            <div class="flex flex-col items-center gap-2">
                <div class="w-6 h-6 border-2 border-gray-200 border-t-transparent rounded-md animate-spin"></div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Memuat...</span>
            </div>
        </div>

        <div id="tableContent" class="bg-gray-50 border border-gray-200 rounded-md shadow-sm overflow-hidden transition-all">
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
