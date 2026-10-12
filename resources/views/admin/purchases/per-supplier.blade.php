@extends('layouts.admin')

@section('title', 'Pembelian: ' . $supplier->name)

@section('content')
<div x-data="{
    search: '',
    filter: '',
    start_date: '',
    end_date: '',

    fetchData(pageUrl = null) {
        let baseUrl = '{{ route('admin.purchases.per-supplier', $supplier->id) }}';
        let url = pageUrl ? pageUrl : baseUrl +
            '?search=' + this.search +
            '&filter=' + this.filter +
            '&start_date=' + this.start_date +
            '&end_date=' + this.end_date;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                document.getElementById('tableContent').innerHTML = html;
                this.bindPagination();
            });
    },

    bindPagination() {
        document.querySelectorAll('#tableContent .pagination a[href]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.fetchData(link.href);
            });
        });
    }
}" x-init="bindPagination()" class="max-w-7xl mx-auto space-y-6">

    <!-- Actions Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.purchases.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali ke Daftar
        </a>
    </div>

    <!-- Header Stats -->
    <div class="relative overflow-hidden bg-[#001529] rounded-lg p-8 text-white shadow-sm">
        <div class="absolute right-0 top-0 w-64 h-64 bg-black/10 rounded-md blur-3xl -mr-20 -mt-20"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-black font-semibold text-[10px] uppercase tracking-[0.3em]">
                    <span class="iconify" data-icon="mdi:warehouse"></span>
                    History Supplier
                </div>
                <h1 class="text-3xl font-semibold tracking-tight">{{ $supplier->name }}</h1>
                <div class="flex items-center gap-4 text-gray-500 text-sm font-medium">
                    <span class="flex items-center gap-1.5 border-r border-slate-700 pr-4">
                        <span class="iconify text-black" data-icon="mdi:file-document-outline"></span>
                        {{ $total }} Transaksi
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="iconify text-emerald-400" data-icon="mdi:phone"></span>
                        {{ $supplier->phone ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.purchases.create', $supplier->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-black text-white rounded-md hover:bg-gray-800 transition-all font-bold shadow-sm  active:scale-95">
                    <span class="iconify text-xl font-semibold" data-icon="mdi:plus"></span>
                    Beli Barang
                </a>
                <a :href="`{{ route('admin.purchases.download', $supplier->id) }}?search=` + search + `&filter=` + filter + `&start_date=` + start_date + `&end_date=` + end_date"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50/10 text-white border border-white/10 rounded-md hover:bg-gray-50/20 transition-all font-bold backdrop-blur-sm">
                    <span class="iconify text-xl" data-icon="mdi:file-pdf-box"></span>
                    Unduh Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 shadow-sm overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <!-- Global Search -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <span class="iconify text-lg" data-icon="mdi:magnify"></span>
                </div>
                <input type="text" x-model="search" @input.debounce.500ms="fetchData()"
                    placeholder="Cari transaksi atau barang..."
                    class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-medium">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select x-model="filter" @change="start_date=''; end_date=''; fetchData()"
                    class="px-4 py-3 bg-white border border-gray-200 rounded-md text-sm font-bold text-slate-700 min-w-[140px] focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 transition-all cursor-pointer">
                    <option value="">Status Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="month">Bulan Ini</option>
                </select>

                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-md p-1 px-3">
                    <span class="iconify text-gray-500" data-icon="mdi:calendar-range"></span>
                    <input type="date" x-model="start_date" @change="filter=''; fetchData()"
                        class="bg-transparent border-none text-xs font-bold text-gray-600 focus:ring-0 p-1">
                    <span class="text-slate-300 px-1">—</span>
                    <input type="date" x-model="end_date" @change="filter=''; fetchData()"
                        class="bg-transparent border-none text-xs font-bold text-gray-600 focus:ring-0 p-1">
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div id="tableContent">
            @include('admin.purchases.partials.table')
        </div>
    </div>

</div>
@endsection
