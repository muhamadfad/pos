@extends('layouts.admin')

@section('title', 'Detail Return')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.stock-returns.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-800">Detail Return</h1>
        </div>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:printer-outline"></span>
            Cetak Surat Jalan
        </button>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[11px] font-black uppercase tracking-widest border border-indigo-100">
                    <span class="iconify" data-icon="mdi:truck-delivery-outline"></span>
                    Surat Jalan / Return
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ $return->reference_number }}</h2>
                    <p class="text-slate-500 font-medium">{{ \Carbon\Carbon::parse($return->transfer_date)->format('d F Y') }}</p>
                </div>
            </div>

            <div class="text-left md:text-right space-y-4">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Asal Cabang</p>
                <div>
                    <h3 class="text-xl font-bold text-slate-900">{{ $return->fromBranch->name }}</h3>
                    <p class="text-slate-500 text-sm">
                        {{ $return->fromBranch->address ?? '-' }}<br>
                        Tel: {{ $return->fromBranch->phone ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if($return->notes)
        <div class="px-8 py-4 bg-slate-50/50 border-b border-slate-100">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Catatan</p>
            <p class="text-sm text-slate-700 italic">{{ $return->notes }}</p>
        </div>
        @endif

        <div class="p-0 sm:p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-400 font-bold text-[11px] uppercase tracking-widest border-b border-slate-100">
                            <th class="px-4 py-4 text-left">Nama Bahan Baku</th>
                            <th class="px-4 py-4 text-center">Jumlah Dikirim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($return->items as $item)
                        <tr>
                            <td class="px-4 py-5 font-bold text-slate-800">
                                {{ $item->ingredient->name ?? 'Unknown Item' }}
                            </td>
                            <td class="px-4 py-5 text-center font-bold text-indigo-600">
                                {{ floatval($item->quantity) }} 
                                <span class="text-[10px] font-medium text-slate-400">{{ $item->ingredient->unit->name ?? 'pcs' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="p-8 border-t border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Dibuat Oleh</p>
                <p class="text-sm font-semibold text-slate-700 mt-1">{{ $return->user->name ?? 'Sistem' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</p>
                <p class="text-emerald-600 font-bold mt-1 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:check-circle"></span> Selesai
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
