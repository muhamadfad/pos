@extends('layouts.admin')

@section('title', 'Daftar Pembelian')

@section('content')
<div class="max-w-[1400px] mx-auto p-6 bg-gray-50 min-h-screen font-sans antialiased text-black">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-md bg-black animate-pulse"></span>
                <span class="text-[10px] font-semibold uppercase tracking-[0.3em] text-gray-500">Inventory Management</span>
            </div>
            <h1 class="text-3xl font-semibold tracking-tighter text-black uppercase italic">Purchasing</h1>
        </div>

        <div class="flex items-center gap-2">
            <div class="relative group">
                <span class="iconify absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" data-icon="lucide:search"></span>
                <input type="text" x-model="search" placeholder="Quick Search..." 
                    class="h-10 w-64 pl-10 pr-4 bg-gray-50 border border-gray-200 rounded-md text-xs font-bold focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all placeholder:text-slate-300 shadow-sm">
            </div>
            <a href="{{ route('admin.purchases.select-supplier') }}"
                class="h-10 px-6 bg-[#001529] text-white text-[11px] font-semibold rounded-md hover:bg-black flex items-center gap-2 transition-all active:scale-95 shadow-sm  uppercase tracking-widest">
                <span class="iconify text-lg" data-icon="lucide:plus"></span>
                Add Purchase
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-sm group hover:border-gray-200 transition-all">
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Volume</p>
            <div class="flex items-baseline gap-2 mt-1">
                <p class="text-3xl font-semibold text-black tracking-tighter">{{ $stats['total'] }}</p>
                <span class="text-[10px] font-bold text-slate-300 uppercase">Transactions</span>
            </div>
        </div>
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-sm group hover:border-gray-200 transition-all border-b-4 border-b-emerald-500">
            <p class="text-[10px] font-semibold text-black uppercase tracking-widest">Cleared</p>
            <div class="flex items-baseline gap-2 mt-1">
                <p class="text-3xl font-semibold text-black tracking-tighter">{{ $stats['paid'] }}</p>
                <span class="text-[10px] font-bold text-slate-300 uppercase">Paid in Full</span>
            </div>
        </div>
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-sm group hover:border-orange-100 transition-all border-b-4 border-b-orange-500">
            <p class="text-[10px] font-semibold text-orange-500 uppercase tracking-widest">Liability</p>
            <div class="flex items-baseline gap-2 mt-1">
                <p class="text-3xl font-semibold text-black tracking-tighter">{{ $stats['debt'] }}</p>
                <span class="text-[10px] font-bold text-slate-300 uppercase text-orange-400">Pending Pay</span>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-50 bg-white flex items-center justify-between">
            <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-gray-500 italic">Master Ledger</span>
            <div class="flex gap-2">
                <div class="w-2 h-2 rounded-md bg-slate-200"></div>
                <div class="w-2 h-2 rounded-md bg-slate-200"></div>
            </div>
        </div>
        
        <div class="p-0">
            @include('admin.purchases.partials.table')
        </div>
    </div>
</div>

<style>
    /* CSS Master untuk Kesan Premium */
    body { background-color: #F9FAFB !important; }

    /* Baris Tabel Rapat tapi Mewah */
    table tbody tr {
        transition: all 0.2s ease;
    }
    table tbody tr:hover {
        background-color: #F8FAFC;
        transform: scale(1.002);
    }
    table tbody td {
        padding: 14px 24px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #1E293B !important; /* Slate-800 */
        border: none !important;
    }

    /* Badge Minimalis & Padat */
    .badge-status {
        font-size: 10px;
        font-weight: 900;
        padding: 4px 10px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Penyesuaian Pagination agar Modern */
    .pagination-modern {
        display: flex;
        gap: 6px;
        justify-content: end;
        padding: 20px;
    }
</style>
@endsection
