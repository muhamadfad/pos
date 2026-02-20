@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.products.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-slate-800">{{ $product->name }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Detail & riwayat transaksi produk</p>
        </div>
        <a href="{{ route('admin.products.edit', $product) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
            <span class="iconify" data-icon="mdi:pencil-outline"></span>
            Edit Produk
        </a>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kategori</p>
            <p class="text-base font-bold text-slate-800">{{ $product->category->name ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Harga Beli</p>
            <p class="text-base font-bold text-slate-800">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Harga Jual</p>
            <p class="text-base font-bold text-emerald-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Stok Saat Ini</p>
            @if($product->stock == 0)
            <p class="text-base font-bold text-red-600 flex items-center gap-1">
                <span class="iconify" data-icon="mdi:alert-circle-outline" data-width="18"></span>
                Habis
            </p>
            @elseif($product->stock <= 5) <p class="text-base font-bold text-amber-600">{{ $product->stock }} unit</p>
                @else
                <p class="text-base font-bold text-slate-800">{{ $product->stock }} unit</p>
                @endif
        </div>
    </div>

    <!-- Barcode & Margin -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                <span class="iconify text-xl" data-icon="mdi:barcode"></span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Barcode</p>
                <p class="font-mono font-semibold text-slate-800 mt-0.5">{{ $product->barcode }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                <span class="iconify text-xl" data-icon="mdi:chart-line"></span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Margin Keuntungan</p>
                @php
                $margin = $product->selling_price - $product->purchase_price;
                $marginPct = $product->purchase_price > 0
                ? round(($margin / $product->purchase_price) * 100, 1)
                : 0;
                @endphp
                <p class="font-semibold text-indigo-700 mt-0.5">
                    Rp {{ number_format($margin, 0, ',', '.') }}
                    <span class="text-xs text-slate-500 font-normal">({{ $marginPct }}%)</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Tabs: Riwayat Transaksi -->
    <div x-data="{ tab: 'outgoing' }" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <!-- Tab Header -->
        <div class="flex border-b border-slate-100">
            <button @click="tab = 'outgoing'"
                class="flex-1 flex items-center justify-center gap-2 px-6 py-4 text-sm font-semibold transition border-b-2"
                :class="tab === 'outgoing'
                    ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50'
                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                <span class="iconify" data-icon="mdi:arrow-top-right-bold-outline"></span>
                Barang Keluar
                <span class="px-2 py-0.5 text-xs rounded-full font-bold"
                    :class="tab === 'outgoing' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'">
                    {{ count($outgoing) }}
                </span>
            </button>
            <button @click="tab = 'incoming'"
                class="flex-1 flex items-center justify-center gap-2 px-6 py-4 text-sm font-semibold transition border-b-2"
                :class="tab === 'incoming'
                    ? 'border-emerald-600 text-emerald-600 bg-emerald-50/50'
                    : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                <span class="iconify" data-icon="mdi:arrow-bottom-left-bold-outline"></span>
                Barang Masuk
                <span class="px-2 py-0.5 text-xs rounded-full font-bold"
                    :class="tab === 'incoming' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                    {{ count($incoming) }}
                </span>
            </button>
        </div>

        <!-- Tab: Barang Keluar -->
        <div x-show="tab === 'outgoing'" x-transition>
            @if($outgoing->isEmpty())
            <div class="flex flex-col items-center gap-3 py-16 text-slate-400">
                <span class="iconify text-5xl" data-icon="mdi:truck-fast-outline"></span>
                <p class="font-medium">Belum ada transaksi keluar</p>
            </div>
            @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            #</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Customer</th>
                        <th
                            class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Qty</th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Harga Jual</th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($outgoing as $i => $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-xs text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ optional($item->order->date)->format('d M Y') ??
                            optional($item->order->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($item->order->customer)
                            <span class="inline-flex items-center gap-1.5 text-slate-700">
                                <span class="iconify text-slate-400" data-icon="mdi:account-outline"></span>
                                {{ $item->order->customer->name }}
                            </span>
                            @else
                            <span class="text-slate-400 italic text-xs">Umum</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span
                                class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-600">
                                -{{ $item->quantity }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right text-slate-600 text-xs">
                            Rp {{ number_format($item->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-800">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-sm font-semibold text-slate-600">Total</td>
                        <td class="px-5 py-3 text-center font-bold text-red-600">
                            -{{ $outgoing->sum('quantity') }}
                        </td>
                        <td></td>
                        <td class="px-5 py-3 text-right font-bold text-slate-800">
                            Rp {{ number_format($outgoing->sum('subtotal'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>

        <!-- Tab: Barang Masuk -->
        <div x-show="tab === 'incoming'" x-transition>
            @if($incoming->isEmpty())
            <div class="flex flex-col items-center gap-3 py-16 text-slate-400">
                <span class="iconify text-5xl" data-icon="mdi:warehouse"></span>
                <p class="font-medium">Belum ada transaksi masuk</p>
            </div>
            @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            #</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Supplier</th>
                        <th
                            class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Qty</th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Harga Beli</th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($incoming as $i => $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-xs text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ optional($item->purchase->date)->format('d M Y') ??
                            optional($item->purchase->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($item->purchase->supplier)
                            <span class="inline-flex items-center gap-1.5 text-slate-700">
                                <span class="iconify text-slate-400" data-icon="mdi:truck-outline"></span>
                                {{ $item->purchase->supplier->name }}
                            </span>
                            @else
                            <span class="text-slate-400 italic text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span
                                class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-600">
                                +{{ $item->quantity }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right text-slate-600 text-xs">
                            Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-800">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-sm font-semibold text-slate-600">Total</td>
                        <td class="px-5 py-3 text-center font-bold text-emerald-600">
                            +{{ $incoming->sum('quantity') }}
                        </td>
                        <td></td>
                        <td class="px-5 py-3 text-right font-bold text-slate-800">
                            Rp {{ number_format($incoming->sum('subtotal'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>

    </div>

</div>
@endsection
