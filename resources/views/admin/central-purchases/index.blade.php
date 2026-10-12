@extends('layouts.admin')

@section('title', 'Pembelian Pusat')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-black">Pembelian Pusat</h1>
        <p class="text-gray-500 text-sm mt-1">Catat belanja barang yang masuk ke Gudang Pusat.</p>
    </div>
    <a href="{{ route('admin.central-purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-black hover:bg-gray-800 text-white text-sm font-semibold rounded-md shadow-sm transition-all active:scale-[0.98]">
        <span class="iconify text-lg" data-icon="mdi:plus"></span>
        Tambah Belanja Pusat
    </a>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gray-50 p-6 rounded-md border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-md bg-gray-50 text-black flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:file-document-outline"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-black">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 p-6 rounded-md border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-md bg-white border border-gray-200 text-black flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:check-circle-outline"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Lunas</p>
                <h3 class="text-2xl font-bold text-black">{{ number_format($stats['paid']) }}</h3>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 p-6 rounded-md border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-md bg-gray-50 text-black flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="mdi:clock-outline"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Lunas (Hutang)</p>
                <h3 class="text-2xl font-bold text-black">{{ number_format($stats['debt']) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 rounded-md shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-4 py-2 font-medium border-b border-gray-200">No. Ref & Tanggal</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200">Supplier</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200">Item Dibeli</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200 text-right">Total Transaksi</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200 text-center">Status</th>
                    <th class="px-4 py-2 font-medium border-b border-gray-200 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-white transition-colors">
                    <td class="px-4 py-2">
                        <div class="font-medium text-black">{{ $purchase->reference_number ?: '-' }}</div>
                        <div class="text-gray-500 text-xs mt-0.5">{{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</div>
                        @if($purchase->branch && $purchase->branch->code !== 'PUSAT')
                            <div class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-50 text-black" title="Dana dari kas cabang">
                                <span class="iconify" data-icon="mdi:wallet"></span>
                                {{ $purchase->branch->name }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <span class="iconify text-gray-500" data-icon="mdi:truck-outline"></span>
                            <span class="font-medium text-slate-700">{{ $purchase->supplier->name ?? 'Tanpa Supplier' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-gray-600 text-xs">
                            @php
                                $itemsToDisplay = $purchase->items->take(3);
                                $remainingCount = $purchase->items->count() - 3;
                            @endphp
                            <ul class="list-disc list-inside">
                            @foreach($itemsToDisplay as $item)
                                <li>{{ $item->ingredient->name ?? 'Unknown' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? 'pcs' }})</li>
                            @endforeach
                            @if($remainingCount > 0)
                                <li class="text-gray-500 text-[10px] mt-1 italic">+ {{ $remainingCount }} item lainnya</li>
                            @endif
                            </ul>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="font-bold text-black">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($purchase->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white border border-gray-200 text-black border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-md bg-white border border-gray-2000"></span> Lunas
                            </span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white border border-gray-200 text-black border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-md bg-white border border-gray-2000"></span> Sebagian
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-rose-50 text-rose-600 border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-md bg-rose-500"></span> Belum Bayar
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.central-purchases.show', $purchase->id) }}" class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-colors" title="Detail">
                                <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data pembelian pusat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($purchases->hasPages())
    <div class="p-6 border-t border-gray-200">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection
