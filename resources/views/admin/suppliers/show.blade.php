@extends('layouts.admin')

@section('title', 'Supplier: ' . $supplier->name)

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.suppliers.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Supplier</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-indigo-600">{{ $supplier->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $supplier->name }}</h1>
        </div>
        <a href="{{ route('admin.suppliers.edit', $supplier) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
            <span class="iconify" data-icon="mdi:pencil-outline"></span>
            Edit Supplier
        </a>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Info Kontak -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Info Kontak</p>
            @if($supplier->type)
            <div class="flex items-center gap-2">
                <span class="iconify text-slate-400" data-icon="mdi:tag-outline"></span>
                <span class="text-sm text-slate-600">{{ $supplier->type }}</span>
            </div>
            @endif
            @if($supplier->phone)
            <div class="flex items-center gap-2">
                <span class="iconify text-slate-400" data-icon="mdi:phone-outline"></span>
                <a href="tel:{{ $supplier->phone }}" class="text-sm text-indigo-600 hover:underline">{{ $supplier->phone
                    }}</a>
            </div>
            @endif
            @if($supplier->address)
            <div class="flex items-start gap-2">
                <span class="iconify text-slate-400 mt-0.5" data-icon="mdi:map-marker-outline"></span>
                <span class="text-sm text-slate-600">{{ $supplier->address }}</span>
            </div>
            @endif
            @if(!$supplier->phone && !$supplier->address && !$supplier->type)
            <p class="text-sm text-slate-400 italic">Tidak ada info kontak</p>
            @endif
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Transaksi</p>
            <p class="text-3xl font-bold text-slate-800">{{ $totalTransaksi }}</p>
            <p class="text-xs text-slate-400 mt-1">kali pembelian</p>
        </div>

        <!-- Total Pembelian -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Pembelian</p>
            <p class="text-xl font-bold text-slate-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
            @if($totalHutang > 0)
            <div
                class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg px-2.5 py-1 w-fit">
                <span class="iconify" data-icon="mdi:alert-circle-outline"></span>
                Hutang: Rp {{ number_format($totalHutang, 0, ',', '.') }}
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

    <!-- Riwayat Transaksi Masuk -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="iconify text-indigo-400" data-icon="mdi:receipt-text-outline"></span>
            <h2 class="font-semibold text-slate-800">Riwayat Transaksi Masuk</h2>
        </div>

        @if($purchases->isEmpty())
        <div class="flex flex-col items-center gap-3 py-16 text-slate-400">
            <span class="iconify text-5xl" data-icon="mdi:warehouse"></span>
            <p class="font-medium">Belum ada transaksi dengan supplier ini</p>
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
                        Sisa Hutang</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($purchases as $i => $purchase)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5 text-center text-xs text-slate-400">{{ $purchases->firstItem() +
                        $loop->index }}</td>
                    <td class="px-5 py-3.5 text-slate-600">
                        {{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">
                            <span class="iconify" data-icon="mdi:package-variant-closed"></span>
                            {{ $purchase->items->count() }} produk
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-slate-800">
                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-right text-slate-600 text-xs">
                        Rp {{ number_format($purchase->total_paid ?? 0, 0, ',', '.') }}
                    </td>
                    <td
                        class="px-5 py-3.5 text-right text-xs {{ ($purchase->remaining_debt ?? 0) > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                        Rp {{ number_format($purchase->remaining_debt ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($purchase->payment_status === 'paid')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                            <span class="iconify" data-icon="mdi:check-circle-outline"></span>
                            Lunas
                        </span>
                        @elseif($purchase->payment_status === 'partial')
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                            <span class="iconify" data-icon="mdi:clock-outline"></span>
                            Sebagian
                        </span>
                        @else
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-600">
                            <span class="iconify" data-icon="mdi:alert-circle-outline"></span>
                            Belum Bayar
                        </span>
                        @endif
                    </td>
                </tr>

                {{-- Detail item produk --}}
                @if($purchase->items->count())
                <tr class="bg-slate-50/60">
                    <td></td>
                    <td colspan="6" class="px-5 py-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach($entry->items as $item)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs text-slate-600">
                                <span class="iconify text-indigo-400 shrink-0"
                                    data-icon="mdi:package-variant-closed"></span>
                                <span class="font-medium">{{ $item->product->name ?? 'Produk dihapus' }}</span>
                                <span class="text-slate-400">× {{ $item->quantity }}</span>
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
                        Rp {{ number_format($purchases->sum('total_amount'), 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-slate-600 text-xs">
                        Rp {{ number_format($purchases->sum('total_paid'), 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-red-600 text-xs">
                        Rp {{ number_format($purchases->sum('remaining_debt'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @if($purchases->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $purchases->links() }}
        </div>
        @endif
        @endif

    </div>

</div>
@endsection
