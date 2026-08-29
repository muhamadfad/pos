@extends('layouts.admin')

@section('title', 'Daftar Hutang')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight"><span class="iconify inline-block" data-icon="mdi:credit-card-outline"></span> Kontrol Hutang</h1>
            <p class="text-slate-500 font-medium">Monitoring kewajiban pembayaran ke supplier</p>
        </div>
    </div>

    @if(session('success'))
    <div
        class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-700 shadow-sm animate-fade-in-down">
        <span class="iconify text-xl" data-icon="mdi:check-circle"></span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Outstanding</p>
            <h3 class="text-2xl font-black text-red-600">Rp {{ number_format($purchases->sum('remaining_debt'), 0, ',',
                '.') }}</h3>
            <p class="text-xs text-slate-400 mt-2">Dari {{ $purchases->total() }} invoice tertunggak</p>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Tanggal /
                            Invoice</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Supplier</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Item Barang</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right">
                            Total Transaksi</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right">
                            Sisa Hutang</th>
                        <th
                            class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($purchases as $purchase)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-700">{{
                                \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</div>
                            <div class="text-[11px] font-medium text-slate-400">#PUR-{{ str_pad($purchase->id, 5, '0',
                                STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4">
    <div class="text-sm font-bold text-slate-800">{{ $purchase->supplier->name ?? '-' }}</div>
    <div class="text-[11px] text-slate-400">{{ $purchase->supplier->phone ?? 'No Phone' }}</div>
</td>
<td class="px-6 py-4">
    <div class="flex flex-col gap-1 max-h-[80px] overflow-y-auto min-w-[150px]">
        @forelse($purchase->items as $item)
            <div class="text-xs text-slate-600 truncate" title="{{ $item->ingredient->name ?? ' }}">
                • {{ $item->ingredient->name ?? ' }} ({{ $item->quantity }})
            </div>
        @empty
            <span class="text-xs text-slate-400">-</span>
        @endforelse
    </div>
</td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-bold text-slate-500">Rp {{ number_format($purchase->total_amount,
                                0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-base font-black text-red-600">Rp {{
                                number_format($purchase->remaining_debt, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-slate-400">Lunas: Rp {{ number_format($purchase->total_paid, 0,
                                ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($purchase->payment_status === 'partial')
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-tight">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                Sebagian
                            </span>
                            @else
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-100 uppercase tracking-tight">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Outstanding
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.purchases.show', $purchase->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 border border-indigo-200 rounded-xl hover:bg-indigo-600 hover:text-white transition-all text-xs font-bold shadow-sm active:scale-95">
                                <span class="iconify" data-icon="mdi:cash-multiple"></span>
                                Bayar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                                <span class="iconify text-3xl" data-icon="mdi:check-all"></span>
                            </div>
                            <p class="text-slate-400 font-bold">Semua hutang terjadwal telah lunas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
