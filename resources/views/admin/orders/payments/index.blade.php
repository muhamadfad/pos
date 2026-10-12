@extends('layouts.admin')

@section('title', 'Daftar Piutang')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-black tracking-tight">💰 Kontrol Piutang</h1>
            <p class="text-gray-500 font-medium">Monitoring tagihan pembayaran dari pelanggan</p>
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
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-1">Total Piutang</p>
            <h3 class="text-2xl font-semibold text-orange-600">Rp {{ number_format($orders->sum('remaining_receivable'), 0,
                ',', '.') }}</h3>
            <p class="text-xs text-gray-500 mt-2">Dari {{ $orders->total() }} tagihan berjalan</p>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tgl / No.
                            Invoice</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Nama
                            Pelanggan</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-right">
                            Total Tagihan</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-right">
                            Sisa Piutang</th>
                        <th
                            class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-center">
                            Status</th>
                        <th class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest text-right">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($orders as $order)
                    <tr class="group hover:bg-white transition-colors">
                        <td class="px-4 py-2">
                            <div class="text-sm font-bold text-slate-700">{{
                                \Carbon\Carbon::parse($order->date)->format('d M Y') }}</div>
                            <div class="text-[11px] font-medium text-gray-500 tracking-tight">{{ $order->invoice_number
                                }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="text-sm font-bold text-black">{{ $order->customer->name ?? 'Pelanggan Umum'
                                }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="px-1.5 py-0.5 rounded bg-slate-100 text-gray-500 text-[9px] font-bold uppercase">{{
                                    $order->customer->type ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-sm font-bold text-gray-500 font-mono">Rp {{
                                number_format($order->total_amount, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="text-base font-semibold text-orange-600 font-mono">Rp {{
                                number_format($order->remaining_receivable, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500">Diterima: Rp {{ number_format($order->total_paid, 0,
                                ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-2 text-center text-[10px]">
                            @if($order->payment_status === 'partial')
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md font-semibold bg-white border border-gray-200 text-black border border-gray-200 uppercase tracking-tighter">
                                <span class="w-1.5 h-1.5 rounded-md bg-white border border-gray-2000 animate-pulse"></span>
                                Partial
                            </span>
                            @else
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-md font-semibold bg-gray-50 text-black border border-gray-200 uppercase tracking-tighter">
                                <span class="w-1.5 h-1.5 rounded-md bg-gray-500"></span>
                                Unpaid
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-black border border-gray-200 rounded-md hover:bg-black text-white hover:text-white transition-all text-xs font-bold shadow-sm active:scale-95">
                                <span class="iconify text-lg" data-icon="mdi:cash-plus"></span>
                                Terima
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="mb-3">
                                <span class="iconify text-4xl opacity-20 mx-auto" data-icon="mdi:cash-check"></span>
                            </div>
                            <p class="font-bold text-sm tracking-tight italic opacity-60">Semua piutang telah ditagih.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->count() > 0)
        <div class="px-4 py-2 bg-white border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
