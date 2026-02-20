@extends('layouts.admin')

@section('title', 'Customer: ' . $customer->name)

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.customers.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-indigo-600">{{ $customer->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $customer->name }}</h1>
        </div>
        <a href="{{ route('admin.customers.edit', $customer) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
            <span class="iconify" data-icon="mdi:pencil-outline"></span>
            Edit Customer
        </a>
    </div>

    <!-- Info + Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Info Kontak -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Info Kontak</p>
            @php
            $typeColor = match($customer->type) {
            'store' => 'bg-blue-50 text-blue-700',
            'project' => 'bg-emerald-50 text-emerald-700',
            'retail' => 'bg-amber-50 text-amber-700',
            default => 'bg-slate-100 text-slate-600',
            };
            @endphp
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $typeColor }}">
                    {{ ucfirst($customer->type) }}
                </span>
            </div>
            @if($customer->phone)
            <div class="flex items-center gap-2">
                <span class="iconify text-slate-400" data-icon="mdi:phone-outline"></span>
                <a href="tel:{{ $customer->phone }}" class="text-sm text-indigo-600 hover:underline">{{ $customer->phone
                    }}</a>
            </div>
            @endif
            @if($customer->address)
            <div class="flex items-start gap-2">
                <span class="iconify text-slate-400 mt-0.5" data-icon="mdi:map-marker-outline"></span>
                <span class="text-sm text-slate-600">{{ $customer->address }}</span>
            </div>
            @endif
            @if(!$customer->phone && !$customer->address)
            <p class="text-sm text-slate-400 italic">Tidak ada info kontak</p>
            @endif
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Transaksi</p>
            <p class="text-3xl font-bold text-slate-800">{{ $totalTransaksi }}</p>
            <p class="text-xs text-slate-400 mt-1">kali pembelian</p>
        </div>

        <!-- Total & Piutang -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Pembelian</p>
            <p class="text-xl font-bold text-slate-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
            @if($totalPiutang > 0)
            <div
                class="mt-2 flex items-center gap-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg px-2.5 py-1 w-fit">
                <span class="iconify" data-icon="mdi:clock-alert-outline"></span>
                Piutang: Rp {{ number_format($totalPiutang, 0, ',', '.') }}
            </div>
            @else
            <div
                class="mt-2 flex items-center gap-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-lg px-2.5 py-1 w-fit">
                <span class="iconify" data-icon="mdi:check-circle-outline"></span>
                Lunas
            </div>
            @endif
        </div>

    </div>

    <!-- Riwayat Transaksi Keluar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="iconify text-indigo-400" data-icon="mdi:receipt-text-outline"></span>
            <h2 class="font-semibold text-slate-800">Riwayat Transaksi Keluar</h2>
        </div>

        @if($orders->isEmpty())
        <div class="flex flex-col items-center gap-3 py-16 text-slate-400">
            <span class="iconify text-5xl" data-icon="mdi:cart-outline"></span>
            <p class="font-medium">Belum ada transaksi dengan customer ini</p>
        </div>
        @else
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th
                        class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">
                        #</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Tanggal</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Item</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Total</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Dibayar</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Sisa Piutang</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($orders as $order)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5 text-center text-xs text-slate-400">{{ $orders->firstItem() + $loop->index
                        }}</td>
                    <td class="px-5 py-3.5 text-slate-600">
                        {{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">
                            <span class="iconify" data-icon="mdi:package-variant-closed"></span>
                            {{ $order->items->count() }} produk
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-slate-800">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-right text-slate-600 text-xs">
                        Rp {{ number_format($order->total_paid ?? 0, 0, ',', '.') }}
                    </td>
                    <td
                        class="px-5 py-3.5 text-right text-xs {{ ($order->remaining_receivable ?? 0) > 0 ? 'text-amber-700 font-semibold' : 'text-slate-400' }}">
                        Rp {{ number_format($order->remaining_receivable ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($order->payment_status === 'paid')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                            <span class="iconify" data-icon="mdi:check-circle-outline"></span>
                            Lunas
                        </span>
                        @elseif($order->payment_status === 'partial')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                            <span class="iconify" data-icon="mdi:clock-outline"></span>
                            Sebagian
                        </span>
                        @else
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-600">
                            <span class="iconify" data-icon="mdi:clock-alert-outline"></span>
                            Belum Bayar
                        </span>
                        @endif
                    </td>
                </tr>

                {{-- Detail produk --}}
                @if($order->items->count())
                <tr class="bg-slate-50/60">
                    <td></td>
                    <td colspan="6" class="px-5 py-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach($out->items as $item)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs text-slate-600">
                                <span class="iconify text-indigo-400 shrink-0"
                                    data-icon="mdi:package-variant-closed"></span>
                                <span class="font-medium">{{ $item->product->name ?? 'Produk dihapus' }}</span>
                                <span class="text-slate-400">× {{ $item->quantity }}</span>
                                <span class="text-slate-300">|</span>
                                <span class="text-emerald-600 font-medium">Rp {{ number_format($item->selling_price, 0,
                                    ',', '.') }}</span>
                            </span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif

                @endforeach
            </tbody>
            <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                <tr>
                    <td colspan="3" class="px-5 py-3 text-sm font-semibold text-slate-600">Total</td>
                    <td class="px-5 py-3 text-right font-bold text-slate-800">
                        Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-slate-600 text-xs">
                        Rp {{ number_format($orders->sum('total_paid'), 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-amber-700 text-xs">
                        Rp {{ number_format($orders->sum('remaining_receivable'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $orders->links() }}
        </div>
        @endif
        @endif

    </div>

</div>
@endsection
