@extends('layouts.admin')

@section('title', 'Daftar Penjualan')

@section('content')
<div x-data="{ search: '' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-black">Daftar Penjualan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat penjualan barang ke customer</p>
        </div>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex gap-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="iconify text-gray-500" data-icon="mdi:calendar"></span>
                    </div>
                    <select name="period" onchange="this.form.submit()" class="pl-9 pr-10 py-2.5 bg-gray-50 border border-gray-200 text-slate-700 text-sm rounded-md focus:ring-[#1677ff] focus:border-gray-200 shadow-sm appearance-none cursor-pointer font-medium hover:border-slate-300 transition-colors">
                        <option value="all" {{ request('period', 'all') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="iconify text-gray-500" data-icon="mdi:chevron-down"></span>
                    </div>
                </div>

            @if(isset($branches) && count($branches) > 0)
                <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="iconify text-gray-500" data-icon="mdi:filter-variant"></span>
                </div>
                <select name="branch_id" onchange="this.form.submit()" 
                    class="pl-9 pr-10 py-2.5 bg-gray-50 border border-gray-200 text-slate-700 text-sm rounded-md focus:ring-[#1677ff] focus:border-gray-200 shadow-sm appearance-none cursor-pointer font-medium min-w-[180px] hover:border-slate-300 transition-colors">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="iconify text-gray-500" data-icon="mdi:chevron-down"></span>
                </div>
                </div>
            @endif
            </form>

            <a href="{{ route('admin.orders.select-customer') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-black text-white text-sm font-semibold rounded-md hover:bg-gray-800 active:bg-indigo-800 transition-all shadow-sm  whitespace-nowrap">
                <span class="iconify text-lg" data-icon="mdi:plus"></span>
                Tambah Penjualan
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 p-5 rounded-md border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-md bg-white border border-gray-200 flex items-center justify-center text-black">
                <span class="iconify text-2xl" data-icon="mdi:cash-register"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</p>
                <p class="text-lg font-semibold text-black">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-gray-50 p-5 rounded-md border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-md bg-white border border-gray-200 flex items-center justify-center text-black">
                <span class="iconify text-2xl" data-icon="mdi:check-circle-outline"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lunas</p>
                <p class="text-lg font-semibold text-black">{{ $stats['paid'] }}</p>
            </div>
        </div>
        <div class="bg-gray-50 p-5 rounded-md border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-md bg-orange-50 flex items-center justify-center text-orange-600">
                <span class="iconify text-2xl" data-icon="mdi:account-clock"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Piutang Aktif</p>
                <p class="text-lg font-semibold text-black">{{ $stats['receivable'] }}</p>
            </div>
        </div>
    </div>
    <!-- Table Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        @include('admin.orders.partials.table')
    </div>

</div>
@endsection
