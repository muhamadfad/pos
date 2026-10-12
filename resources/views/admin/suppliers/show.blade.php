@extends('layouts.admin')

@section('title', 'Supplier: ' . $supplier->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.suppliers.index') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-gray-50 border border-gray-200 text-gray-500 hover:text-black hover:bg-gray-50 transition shadow-sm group">
                <span class="iconify text-xl group-active:scale-90 transition-transform" data-icon="lucide:chevron-left"></span>
            </a>
            <div>
                <nav class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Detail Mitra</span>
                    <span class="iconify text-slate-300 text-[10px]" data-icon="lucide:chevron-right"></span>
                    <span class="text-[10px] font-bold text-black uppercase tracking-widest">ID: SUP-{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-black tracking-tight">{{ $supplier->name }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-50 border border-gray-200 text-gray-600 text-sm font-bold rounded-md hover:bg-white transition shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                Edit Supplier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-6 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white rounded-md flex items-center justify-center text-slate-100 italic font-semibold text-4xl select-none">@</div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 relative">Informasi Kontak</p>
            <div class="space-y-3 relative">
                @if($supplier->type)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-md bg-gray-50 flex items-center justify-center text-black shrink-0">
                        <span class="iconify" data-icon="lucide:tag"></span>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">{{ strtoupper($supplier->type) }}</span>
                </div>
                @endif
                @if($supplier->phone)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center text-black shrink-0">
                        <span class="iconify" data-icon="lucide:phone"></span>
                    </div>
                    <a href="tel:{{ $supplier->phone }}" class="text-sm font-bold text-slate-700 hover:text-black transition-colors">{{ $supplier->phone }}</a>
                </div>
                @endif
                @if($supplier->address)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-md bg-rose-50 flex items-center justify-center text-rose-500 shrink-0 mt-0.5">
                        <span class="iconify" data-icon="lucide:map-pin"></span>
                    </div>
                    <span class="text-xs font-medium text-gray-500 leading-relaxed italic">{{ $supplier->address }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Aktivitas Transaksi</p>
            <div class="mt-4">
                <p class="text-3xl font-semibold text-black tracking-tight">{{ $totalTransaksi }}</p>
                <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-tighter">Total Faktur Pembelian</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50">
                <span class="text-[10px] font-bold text-black bg-white border border-gray-200 px-2 py-1 rounded-md">Aktif</span>
            </div>
        </div>

        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Ringkasan Finansial</p>
            <div class="mt-4">
                <p class="text-2xl font-semibold text-black tracking-tight font-mono">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
                <p class="text-[10px] font-bold text-gray-500 mt-1 uppercase">Akumulasi Belanja</p>
            </div>
            <div class="mt-4">
                @if($totalHutang > 0)
                <div class="flex items-center gap-2 text-rose-600 bg-rose-50 px-3 py-1.5 rounded-md border border-rose-100 shadow-sm w-fit">
                    <span class="iconify text-sm" data-icon="lucide:alert-triangle"></span>
                    <span class="text-[11px] font-bold uppercase tracking-tight">Hutang: Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="flex items-center gap-2 text-black bg-white border border-gray-200 px-3 py-1.5 rounded-md border border-gray-200 w-fit">
                    <span class="iconify text-sm" data-icon="lucide:check-circle"></span>
                    <span class="text-[11px] font-bold uppercase tracking-tight">Status Finansial: Aman</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-md bg-black flex items-center justify-center text-white shadow-sm">
                    <span class="iconify" data-icon="lucide:file-text"></span>
                </div>
                <h2 class="font-bold text-black tracking-tight">Riwayat Pembelian Stok</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($purchases->isEmpty())
            <div class="flex flex-col items-center gap-3 py-20 text-slate-300">
                <span class="iconify text-6xl opacity-20" data-icon="lucide:box"></span>
                <p class="font-bold text-sm">Belum ada catatan transaksi masuk</p>
            </div>
            @else
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest w-12 text-center">#</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Nilai Faktur</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Terbayar</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Tunggakan</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchases as $purchase)
                    <tr class="group hover:bg-white transition-colors">
                        <td class="px-6 py-5 text-center text-xs text-gray-500 font-mono">
                            {{ $purchases->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm font-bold text-slate-700 leading-none">
                                {{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}
                            </p>
                            <p class="text-[10px] font-bold text-gray-500 mt-1 uppercase tracking-tighter">
                                ID: TRX-{{ $purchase->id }}
                            </p>
                        </td>
                        <td class="px-6 py-5 text-right font-semibold text-black text-sm font-mono">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-5 text-right text-xs font-bold text-gray-500">
                            Rp {{ number_format($purchase->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-semibold font-mono {{ ($purchase->remaining_debt ?? 0) > 0 ? 'text-rose-500' : 'text-slate-300' }}">
                                Rp {{ number_format($purchase->remaining_debt ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @php
                                $statusMap = [
                                    'paid' => ['bg-white border border-gray-200 text-black border-gray-200', 'Lunas', 'lucide:check'],
                                    'partial' => ['bg-gray-50 text-black border-gray-200', 'Sebagian', 'lucide:clock'],
                                    'unpaid' => ['bg-rose-50 text-rose-600 border-rose-100', 'Belum Bayar', 'lucide:x-circle'],
                                ];
                                $s = $statusMap[$purchase->payment_status] ?? $statusMap['unpaid'];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $s[0] }}">
                                <span class="iconify" data-icon="{{ $s[2] }}"></span>
                                {{ strtoupper($s[1]) }}
                            </span>
                        </td>
                    </tr>

                    {{-- Perbaikan Foreach pada Item Produk --}}
                    @if($purchase->items->count())
                    <tr class="bg-white">
                        <td></td>
                        <td colspan="5" class="px-4 py-2 border-l-2 border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                @foreach($purchase->items as $item) {{-- Variabel sudah diperbaiki dari $entry ke $purchase --}}
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md shadow-sm">
                                    <span class="iconify text-black text-sm" data-icon="lucide:package"></span>
                                    <span class="text-xs font-bold text-slate-700">{{ $item->ingredient->name ?? 'Bahan baku dihapus' }}</span>
                                    <span class="w-1 h-1 rounded-md bg-slate-300"></span>
                                    <span class="text-xs font-semibold text-black tracking-tighter">{{ $item->quantity }}x</span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        @if($purchases->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
