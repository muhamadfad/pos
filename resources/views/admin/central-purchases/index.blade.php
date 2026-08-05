@extends('layouts.admin')

@section('title', 'Pembelian Pusat')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Pembelian Pusat</h1>
        <p class="text-slate-500 text-sm mt-1">Catat belanja barang yang masuk ke Gudang Pusat.</p>
    </div>
    <a href="{{ route('central-purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98]">
        <span class="iconify text-lg" data-icon="mdi:plus"></span>
        Tambah Belanja Pusat
    </a>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:file-document-outline"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:check-circle-outline"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Lunas</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['paid']) }}</h3>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:clock-outline"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Belum Lunas (Hutang)</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ number_format($stats['debt']) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium border-b border-slate-200">No. Ref & Tanggal</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Supplier</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-right">Total Transaksi</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-center">Status</th>
                    <th class="px-6 py-4 font-medium border-b border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $purchase->reference_number ?: '-' }}</div>
                        <div class="text-slate-500 text-xs mt-0.5">{{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-slate-400" data-icon="mdi:truck-outline"></span>
                            <span class="font-medium text-slate-700">{{ $purchase->supplier->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="font-bold text-slate-800">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($purchase->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas
                            </span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Sebagian
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Belum Bayar
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('central-purchases.show', $purchase->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Detail">
                                <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        Belum ada data pembelian pusat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($purchases->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection
