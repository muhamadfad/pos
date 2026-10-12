@extends('layouts.admin')

@section('title', 'Daftar Hutang')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-black tracking-tight"><span class="iconify inline-block" data-icon="mdi:credit-card-outline"></span> Kontrol Hutang</h1>
            <p class="text-gray-500 font-medium">Monitoring kewajiban pembayaran ke supplier</p>
        </div>
    </div>

    @if(session('success'))
    <div
        class="p-4 bg-white border border-gray-200 border border-gray-200 rounded-md flex items-center gap-3 text-black shadow-sm animate-fade-in-down">
        <span class="iconify text-xl" data-icon="mdi:check-circle"></span>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-50 p-6 rounded-md border border-gray-200 shadow-sm">
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-1">Total Outstanding</p>
            <h3 class="text-2xl font-semibold text-red-600">Rp {{ number_format($purchases->sum('remaining_debt'), 0, ',',
                '.') }}</h3>
            <p class="text-xs text-gray-500 mt-2">Dari {{ $purchases->total() }} invoice tertunggak</p>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tanggal /
                            Invoice</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Supplier</th>
<th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Item Barang</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-right">
                            Total Transaksi</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-right">
                            Sisa Hutang</th>
                        <th
                            class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-center">
                            Status</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-right">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($purchases as $purchase)
                    <tr class="group hover:bg-white transition-colors">
                        <td class="px-4 py-2">
                            <div class="text-sm font-bold text-slate-700">{{
                                \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</div>
                            <div class="text-[11px] font-medium text-gray-500">#PUR-{{ str_pad($purchase->id, 5, '0',
                                STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-4 py-2">
    <div class="text-sm font-bold text-black">{{ $purchase->supplier->name ?? '-' }}</div>
    <div class="text-[11px] text-gray-500">{{ $purchase->supplier->phone ?? 'No Phone' }}</div>
</td>
<td class="px-4 py-2">
    <div class="flex flex-col gap-1 max-h-[80px] overflow-y-auto min-w-[150px]">
        @forelse($purchase->items as $item)
            <div class="text-xs text-gray-600 truncate" title="{{ $item->ingredient->name ?? ' }}">
                • {{ $item->ingredient->name ?? ' }} ({{ $item->quantity }})
            </div>
        @empty
            <span class="text-xs text-gray-500">-</span>
        @endforelse
    </div>
</td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-sm font-bold text-gray-500">Rp {{ number_format($purchase->total_amount,
                                0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-base font-semibold text-red-600">Rp {{
                                number_format($purchase->remaining_debt, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500">Lunas: Rp {{ number_format($purchase->total_paid, 0,
                                ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($purchase->payment_status === 'partial')
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-semibold bg-gray-50 text-black border border-gray-200 uppercase tracking-tight">
                                <span class="w-1.5 h-1.5 rounded-md bg-black animate-pulse"></span>
                                Sebagian
                            </span>
                            @else
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-semibold bg-gray-50 text-black border border-gray-200 uppercase tracking-tight">
                                <span class="w-1.5 h-1.5 rounded-md bg-gray-500"></span>
                                Outstanding
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('admin.purchases.show', $purchase->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-black border border-gray-200 rounded-md hover:bg-black hover:text-white transition-all text-xs font-bold shadow-sm active:scale-95">
                                <span class="iconify" data-icon="mdi:cash-multiple"></span>
                                Bayar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-white text-slate-300 mb-4">
                                <span class="iconify text-3xl" data-icon="mdi:check-all"></span>
                            </div>
                            <p class="text-gray-500 font-bold">Semua hutang terjadwal telah lunas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="px-4 py-2 bg-white border-t border-gray-200">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
