@extends('layouts.admin')

@section('title', 'Pembelian Pusat')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Pembelian Pusat</h1>
        <p class="text-slate-500 text-sm mt-1">Catat belanja barang yang masuk ke Gudang Pusat.</p>
    </div>
    <a href="{{ route('admin.central-purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98]">
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
                    <th class="px-6 py-4 font-medium border-b border-slate-200">Item Dibeli</th>
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
                            <span class="font-medium text-slate-700">{{ $purchase->supplier->name ?? 'Tanpa Supplier' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-slate-600 text-xs">
                            @php
                                $itemsToDisplay = $purchase->items->take(3);
                                $remainingCount = $purchase->items->count() - 3;
                            @endphp
                            <ul class="list-disc list-inside">
                            @foreach($itemsToDisplay as $item)
                                <li>{{ $item->ingredient->name ?? 'Unknown' }} ({{ floatval($item->quantity) }} {{ $item->ingredient->unit->name ?? 'pcs' }})</li>
                            @endforeach
                            @if($remainingCount > 0)
                                <li class="text-slate-400 text-[10px] mt-1 italic">+ {{ $remainingCount }} item lainnya</li>
                            @endif
                            </ul>
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
                            <a href="{{ route('admin.central-purchases.show', $purchase->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Detail">
                                <span class="iconify text-lg" data-icon="mdi:eye-outline"></span>
                            </a>
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Produksi (Olah Bahan)</h1>
            <p class="text-slate-500 mt-2 font-medium">Data produksi olah bahan mentah ke bahan jadi di Gudang Pusat</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.central-productions.create') }}" 
               class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-200">
                <span class="iconify mr-2" data-icon="lucide:plus" data-width="18"></span>
                Tambah Produksi
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        
        <!-- Filters -->
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <form action="{{ route('admin.central-productions.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-colors">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 text-white font-semibold text-sm rounded-xl hover:bg-slate-700 transition-colors">
                        Filter Data
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date']))
                        <a href="{{ route('admin.central-productions.index') }}" class="ml-3 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-xs uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-2xl">Ref. No</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Barang Hasil</th>
                        <th class="px-6 py-4 text-right">Yield Aktual</th>
                        <th class="px-6 py-4 text-right">Total Biaya Mentah</th>
                        <th class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    @forelse ($productions as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800">{{ $item->reference_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $item->resultIngredient->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                {{ number_format($item->result_quantity, 1, ',', '.') }} {{ $item->resultIngredient->unit->name ?? 'kg' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-800">
                                Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.central-productions.show', $item->id) }}" 
                                       class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Lihat Detail">
                                        <span class="iconify" data-icon="lucide:eye" data-width="18"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                    <span class="iconify text-slate-400" data-icon="lucide:package-open" data-width="32"></span>
                                </div>
                                <h3 class="text-slate-800 font-semibold mb-1">Belum ada data</h3>
                                <p class="text-slate-500 text-sm">Tidak ada riwayat produksi olah bahan yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $productions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
