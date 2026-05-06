@extends('layouts.admin')

@section('title', 'Manajemen Customer')

@section('content')
<div x-data="customerPage()" class="max-w-6xl mx-auto space-y-6 pb-12">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                <span class="iconify text-2xl" data-icon="lucide:users"></span>
            </div>
            <div>
                <nav class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Database</span>
                    <span class="iconify text-slate-300 text-[10px]" data-icon="lucide:chevron-right"></span>
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest font-mono">CRM-System</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer</h1>
            </div>
        </div>
        
        <a href="{{ route('admin.customers.create') }}"
            class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600 text-white text-sm font-bold rounded-2xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100 active:scale-95 group">
            <span class="iconify text-lg group-hover:rotate-90 transition-transform duration-300" data-icon="lucide:plus"></span>
            Tambah Customer
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                <span class="iconify" data-icon="lucide:user-check"></span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</p>
                <p class="text-xl font-black text-slate-800 leading-none">{{ $total }}</p>
            </div>
        </div>

        <div class="md:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm px-4 py-2 flex items-center gap-3 focus-within:border-indigo-400 focus-within:ring-4 focus-within:ring-indigo-50 transition-all">
            <span class="iconify text-slate-300 text-xl shrink-0" data-icon="lucide:search"></span>
            <input type="text" x-model="search" @input.debounce.400ms="fetchData()"
                placeholder="Cari nama pelanggan, alamat, atau nomor WhatsApp..."
                class="flex-1 bg-transparent text-sm text-slate-700 placeholder-slate-400 font-medium outline-none h-10">
            
            <button x-show="search" @click="search=''; fetchData()" 
                x-transition
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition">
                <span class="iconify" data-icon="lucide:x"></span>
            </button>
        </div>
    </div>

    <div class="relative min-h-[400px]">
        {{-- Loading Overlay --}}
        <div x-show="isLoading" 
             x-transition:enter="transition opacity-30 duration-300"
             x-transition:leave="transition opacity-0 duration-300"
             class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-2xl">
            <div class="flex flex-col items-center gap-2">
                <span class="iconify text-4xl text-indigo-600 animate-spin" data-icon="lucide:loader-2"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Sinkronisasi...</span>
            </div>
        </div>

        <div id="tableContent" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            @include('admin.customers.partials.table')
        </div>
    </div>

</div>

<script>
    function customerPage() {
        return {
            search: '',
            isLoading: false,
            fetchData(url = null) {
                this.isLoading = true;
                let fetchUrl = url || `{{ route('admin.customers.index') }}?search=${encodeURIComponent(this.search)}`;
                
                fetch(fetchUrl, { 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('tableContent').innerHTML = html;
                    if (window.Iconify) window.Iconify.scan();
                    
                    // Smooth scroll top table
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                })
                .catch(err => console.error('Error fetching data:', err))
                .finally(() => {
                    setTimeout(() => { this.isLoading = false; }, 300);
                });
            }
        }
    }
</script>
@endsection