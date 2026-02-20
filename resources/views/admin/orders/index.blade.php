@extends('layouts.admin')

@section('title', 'Daftar Penjualan')

@section('content')
<div x-data="{ search: '' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Penjualan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Riwayat penjualan barang ke customer</p>
        </div>
        <a href="{{ route('admin.orders.select-customer') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all shadow-md shadow-indigo-100">
            <span class="iconify text-lg" data-icon="mdi:plus"></span>
            Tambah Penjualan
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="iconify text-2xl" data-icon="mdi:cash-register"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="iconify text-2xl" data-icon="mdi:check-circle-outline"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Lunas</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['paid'] }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <span class="iconify text-2xl" data-icon="mdi:account-clock"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Piutang Aktif</p>
                <p class="text-xl font-bold text-slate-800">{{ $stats['receivable'] }}</p>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @include('admin.orders.partials.table')
    </div>

</div>
@endsection
