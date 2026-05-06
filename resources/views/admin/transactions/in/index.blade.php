@extends('layouts.admin')

@section('title', 'Barang Masuk')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">📥 Barang Masuk</h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">Pantau dan kelola inventaris barang yang masuk ke gudang.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transactions.in.selectSupplier') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus-circle"></span>
                Tambah Transaksii
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Supplier</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Daftar Barang</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Total Nominal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($stockEntries as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors group align-top">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="text-sm font-bold text-slate-700 block">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</span>
                            <span class="text-[10px] font-mono text-slate-400 uppercase">#IN-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 font-bold text-xs uppercase">
                                    {{ substr($row->supplier->name ?? '?', 0, 2) }}
                                </div>
                                <span class="text-sm font-black text-slate-800 tracking-tight">{{ $row->supplier->name ?? '-' }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="space-y-3">
                                @foreach ($row->items as $item)
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $item->product->name ?? '-' }}</span>
                                    <span class="text-[11px] text-slate-500 font-medium">
                                        {{ $item->quantity }} unit <span class="mx-1">×</span> Rp {{ number_format($item->purchase_price ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-black text-slate-900 tracking-tight">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            @php
                                $isLunas = strtoupper($row->payment_status) === 'LUNAS';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black border uppercase tracking-wider
                                {{ $isLunas 
                                    ? 'bg-emerald-50 text-emerald-600 border-emerald-100' 
                                    : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $isLunas ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $row->payment_status }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.transactions.in.show', $row->id) }}" 
                                    class="p-2 bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 hover:shadow-sm rounded-xl transition-all">
                                    <span class="iconify" data-icon="lucide:eye"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <span class="iconify text-3xl text-slate-300" data-icon="lucide:package-2"></span>
                                </div>
                                <p class="text-sm font-bold text-slate-400 italic">Data barang masuk belum tersedia.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockEntries->hasPages())
        <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
                Menampilkan {{ $stockEntries->firstItem() }}-{{ $stockEntries->lastItem() }} dari {{ $stockEntries->total() }} data
            </p>
            <div class="flex items-center gap-1">
                @if($stockEntries->onFirstPage())
                    <span class="p-2 text-slate-300 cursor-not-allowed"><span class="iconify text-xl" data-icon="lucide:chevron-left"></span></span>
                @else
                    <a href="{{ $stockEntries->previousPageUrl() }}" class="p-2 text-slate-600 hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90 text-xl">
                        <span class="iconify" data-icon="lucide:chevron-left"></span>
                    </a>
                @endif

                @foreach ($stockEntries->getUrlRange(max(1, $stockEntries->currentPage() - 2), min($stockEntries->lastPage(), $stockEntries->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" 
                       class="w-9 h-9 flex items-center justify-center text-xs font-black rounded-xl transition-all 
                       {{ $page == $stockEntries->currentPage() ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-white hover:text-indigo-600 border border-transparent hover:border-slate-200' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($stockEntries->hasMorePages())
                    <a href="{{ $stockEntries->nextPageUrl() }}" class="p-2 text-slate-600 hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90 text-xl">
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