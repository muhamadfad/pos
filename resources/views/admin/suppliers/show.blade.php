@extends('layouts.admin')

@section('title', 'Supplier: ' . $supplier->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.suppliers.index') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition shadow-sm group">
                <span class="iconify text-xl group-active:scale-90 transition-transform" data-icon="lucide:chevron-left"></span>
            </a>
            <div>
                <nav class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Detail Mitra</span>
                    <span class="iconify text-slate-300 text-[10px]" data-icon="lucide:chevron-right"></span>
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">ID: SUP-{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $supplier->name }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-50 transition shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                Edit Supplier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-100 italic font-black text-4xl select-none">@</div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 relative">Informasi Kontak</p>
            <div class="space-y-3 relative">
                @if($supplier->type)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shrink-0">
                        <span class="iconify" data-icon="lucide:tag"></span>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">{{ strtoupper($supplier->type) }}</span>
                </div>
                @endif
                @if($supplier->phone)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 shrink-0">
                        <span class="iconify" data-icon="lucide:phone"></span>
                    </div>
                    <a href="tel:{{ $supplier->phone }}" class="text-sm font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $supplier->phone }}</a>
                </div>
                @endif
                @if($supplier->address)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shrink-0 mt-0.5">
                        <span class="iconify" data-icon="lucide:map-pin"></span>
                    </div>
                    <span class="text-xs font-medium text-slate-500 leading-relaxed italic">{{ $supplier->address }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aktivitas Transaksi</p>
            <div class="mt-4">
                <p class="text-4xl font-black text-slate-900 tracking-tight">{{ $totalTransaksi }}</p>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-tighter">Total Faktur Pembelian</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ringkasan Finansial</p>
            <div class="mt-4">
                <p class="text-2xl font-black text-slate-900 tracking-tight font-mono">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Akumulasi Belanja</p>
            </div>
            <div class="mt-4">
                @if($totalHutang > 0)
                <div class="flex items-center gap-2 text-rose-600 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-100 shadow-sm w-fit">
                    <span class="iconify text-sm" data-icon="lucide:alert-triangle"></span>
                    <span class="text-[11px] font-bold uppercase tracking-tight">Hutang: Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 w-fit">
                    <span class="iconify text-sm" data-icon="lucide:check-circle"></span>
                    <span class="text-[11px] font-bold uppercase tracking-tight">Status Finansial: Aman</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-8">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-sm shadow-indigo-200">
                    <span class="iconify" data-icon="lucide:file-text"></span>
                </div>
                <h2 class="font-bold text-slate-800 tracking-tight">Riwayat Pembelian Stok</h2>
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
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-12 text-center">#</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Nilai Faktur</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Terbayar</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Tunggakan</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchases as $purchase)
                    <tr class="group hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-5 text-center text-xs text-slate-400 font-mono">
                            {{ $purchases->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm font-bold text-slate-700 leading-none">
                                {{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}
                            </p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">
                                ID: TRX-{{ $purchase->id }}
                            </p>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-slate-800 text-sm font-mono">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-5 text-right text-xs font-bold text-slate-500">
                            Rp {{ number_format($purchase->total_paid ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-sm font-black font-mono {{ ($purchase->remaining_debt ?? 0) > 0 ? 'text-rose-500' : 'text-slate-300' }}">
                                Rp {{ number_format($purchase->remaining_debt ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @php
                                $statusMap = [
                                    'paid' => ['bg-emerald-50 text-emerald-600 border-emerald-100', 'Lunas', 'lucide:check'],
                                    'partial' => ['bg-amber-50 text-amber-600 border-amber-100', 'Sebagian', 'lucide:clock'],
                                    'unpaid' => ['bg-rose-50 text-rose-600 border-rose-100', 'Belum Bayar', 'lucide:x-circle'],
                                ];
                                $s = $statusMap[$purchase->payment_status] ?? $statusMap['unpaid'];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $s[0] }}">
                                <span class="iconify" data-icon="{{ $s[2] }}"></span>
                                {{ strtoupper($s[1]) }}
                            </span>
                        </td>
                    </tr>

                    {{-- Perbaikan Foreach pada Item Produk --}}
                    @if($purchase->items->count())
                    <tr class="bg-slate-50/40">
                        <td></td>
                        <td colspan="5" class="px-6 py-3 border-l-2 border-indigo-500">
                            <div class="flex flex-wrap gap-2">
                                @foreach($purchase->items as $item) {{-- Variabel sudah diperbaiki dari $entry ke $purchase --}}
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                                    <span class="iconify text-indigo-500 text-sm" data-icon="lucide:package"></span>
                                    <span class="text-xs font-bold text-slate-700">{{ $item->product->name ?? 'Produk dihapus' }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="text-xs font-black text-indigo-600 tracking-tighter">{{ $item->quantity }}x</span>
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
        <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/30">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>
</div>
@endsection