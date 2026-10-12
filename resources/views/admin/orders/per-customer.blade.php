@extends('layouts.admin')

@section('title', 'Penjualan: ' . $customer->name)

@section('content')
<div x-data="{
    search: '',
    filter: '',
    start_date: '',
    end_date: '',

    fetchData(pageUrl = null) {
        let baseUrl = '{{ route('admin.orders.per-customer', $customer->id) }}';
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
        <a href="{{ route('admin.orders.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali ke Daftar
        </a>
    </div>

    <!-- Header Stats -->
    <div class="relative overflow-hidden bg-[#001529] rounded-lg p-8 text-white shadow-sm">
        <div class="absolute right-0 top-0 w-64 h-64 bg-white border border-gray-2000/10 rounded-md blur-3xl -mr-20 -mt-20"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-emerald-400 font-semibold text-[10px] uppercase tracking-[0.3em]">
                    <span class="iconify" data-icon="mdi:account-star-outline"></span>
                    Portfolio Pelanggan
                </div>
                <h1 class="text-3xl font-semibold tracking-tight">{{ $customer->name }}</h1>
                <div class="flex items-center gap-4 text-gray-500 text-sm font-medium">
                    <span class="flex items-center gap-1.5 border-r border-slate-700 pr-4">
                        <span class="iconify text-emerald-400" data-icon="mdi:history"></span>
                        {{ $total }} Transaksi
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-md bg-gray-50/5 border border-white/10 text-[10px] uppercase font-bold tracking-widest text-emerald-300">
                        {{ $customer->type }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.create', $customer->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-black text-white rounded-md hover:bg-gray-800 transition-all font-bold shadow-sm  active:scale-95">
                    <span class="iconify text-xl font-semibold" data-icon="mdi:cart-plus"></span>
                    Jual Barang
                </a>
                <a :href="`{{ route('admin.orders.download', $customer->id) }}?search=` + search + `&filter=` + filter + `&start_date=` + start_date + `&end_date=` + end_date"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50/10 text-white border border-white/10 rounded-md hover:bg-gray-50/20 transition-all font-bold backdrop-blur-sm">
                    <span class="iconify text-xl" data-icon="mdi:file-pdf-box"></span>
                    Unduh PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 shadow-sm overflow-hidden text-sm">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <!-- Global Search -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <span class="iconify text-lg" data-icon="mdi:magnify"></span>
                </div>
                <input type="text" x-model="search" @input.debounce.500ms="fetchData()"
                    placeholder="Cari dari no. invoice atau produk..."
                    class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-md focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all font-medium">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select x-model="filter" @change="start_date=''; end_date=''; fetchData()"
                    class="px-4 py-3 bg-white border border-gray-200 rounded-md font-bold text-slate-700 min-w-[140px] focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 transition-all cursor-pointer">
                    <option value="">Periode Cepat</option>
                    <option value="today">Hari Ini</option>
                    <option value="month">Bulan Ini</option>
                </select>

                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-md p-1 px-3">
                    <span class="iconify text-gray-500" data-icon="mdi:calendar-month"></span>
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
            @include('admin.orders.partials.table')
        </div>
    </div>

</div>
@endsection
