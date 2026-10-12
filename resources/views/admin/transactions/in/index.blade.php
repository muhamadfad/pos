@extends('layouts.admin')

@section('title', 'Barang Masuk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-black tracking-tight">📥 Barang Masuk</h1>
            <p class="text-sm font-medium text-gray-500 mt-0.5">Pantau dan kelola inventaris barang yang masuk ke gudang.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transactions.in.selectSupplier') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-md hover:bg-gray-800 transition-all shadow-sm  active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus-circle"></span>
                Tambah Transaksii
            </a>
        </div>
    </div>

    <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Supplier</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Daftar Barang</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Total Nominal</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($stockEntries as $row)
                    <tr class="hover:bg-white transition-colors group align-top">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="text-sm font-bold text-slate-700 block">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</span>
                            <span class="text-[10px] font-mono text-gray-500 uppercase">#IN-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-md bg-gray-50 flex items-center justify-center text-black font-bold text-xs uppercase">
                                    {{ substr($row->supplier->name ?? '?', 0, 2) }}
                                </div>
                                <span class="text-sm font-semibold text-black tracking-tight">{{ $row->supplier->name ?? '-' }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="space-y-3">
                                @foreach ($row->items as $item)
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $item->product->name ?? '-' }}</span>
                                    <span class="text-[11px] text-gray-500 font-medium">
                                        {{ $item->quantity }} unit <span class="mx-1">×</span> Rp {{ number_format($item->purchase_price ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-semibold text-black tracking-tight">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            @php
                                $isLunas = strtoupper($row->payment_status) === 'LUNAS';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-semibold border uppercase tracking-wider
                                {{ $isLunas 
                                    ? 'bg-white border border-gray-200 text-black border-gray-200' 
                                    : 'bg-gray-50 text-black border-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-md mr-1.5 {{ $isLunas ? 'bg-white border border-gray-2000' : 'bg-gray-500' }}"></span>
                                {{ $row->payment_status }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.transactions.in.show', $row->id) }}" 
                                    class="p-2 bg-gray-50 border border-gray-200 text-gray-500 hover:text-black hover:border-gray-200 hover:shadow-sm rounded-md transition-all">
                                    <span class="iconify" data-icon="lucide:eye"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-white rounded-md flex items-center justify-center mb-4">
                                    <span class="iconify text-3xl text-slate-300" data-icon="lucide:package-2"></span>
                                </div>
                                <p class="text-sm font-bold text-gray-500 italic">Data barang masuk belum tersedia.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockEntries->hasPages())
        <div class="px-6 py-5 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                Menampilkan {{ $stockEntries->firstItem() }}-{{ $stockEntries->lastItem() }} dari {{ $stockEntries->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($stockEntries->onFirstPage())
                    <span class="p-2 text-slate-300 cursor-not-allowed"><span class="iconify text-xl" data-icon="lucide:chevron-left"></span></span>
                @else
                    <a href="{{ $stockEntries->previousPageUrl() }}" class="p-2 text-gray-600 hover:bg-gray-50 hover:shadow-sm rounded-md transition-all active:scale-90 text-xl">
                        <span class="iconify" data-icon="lucide:chevron-left"></span>
                    </a>
                @endif

                @foreach ($stockEntries->getUrlRange(max(1, $stockEntries->currentPage() - 2), min($stockEntries->lastPage(), $stockEntries->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" 
                       class="w-9 h-9 flex items-center justify-center text-xs font-semibold rounded-md transition-all 
                       {{ $page == $stockEntries->currentPage() ? 'bg-black text-white shadow-sm ' : 'text-gray-500 hover:bg-gray-50 hover:text-black border border-transparent hover:border-gray-200' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($stockEntries->hasMorePages())
                    <a href="{{ $stockEntries->nextPageUrl() }}" class="p-2 text-gray-600 hover:bg-gray-50 hover:shadow-sm rounded-md transition-all active:scale-90 text-xl">
                        <span class="iconify" data-icon="lucide:chevron-right"></span>
                    </a>
                @else
                    <span class="p-2 text-slate-300 cursor-not-allowed"><span class="iconify text-xl" data-icon="lucide:chevron-right"></span></span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
