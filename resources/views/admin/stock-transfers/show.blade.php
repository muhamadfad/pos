@extends('layouts.admin')

@section('title', 'Detail Distribusi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.stock-transfers.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Kembali
            </a>
            <h1 class="text-lg font-semibold text-black">Detail Distribusi</h1>
        </div>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-slate-700 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:printer-outline"></span>
            Cetak Surat Jalan
        </button>
    </div>

    <!-- Info Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden mb-8">
        <div class="p-8 border-b border-gray-200 flex flex-col md:flex-row justify-between gap-8">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 text-black rounded-md text-[11px] font-semibold uppercase tracking-widest border border-gray-200">
                    <span class="iconify" data-icon="mdi:truck-delivery-outline"></span>
                    Surat Jalan / Distribusi
                </div>
                <div>
                    <h2 class="text-3xl font-semibold text-black tracking-tight">{{ $transfer->reference_number }}</h2>
                    <p class="text-gray-500 font-medium">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d F Y') }}</p>
                </div>
            </div>

            <div class="text-left md:text-right space-y-4">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tujuan Cabang</p>
                <div>
                    <h3 class="text-lg font-semibold text-black">{{ $transfer->toBranch->name }}</h3>
                    <p class="text-gray-500 text-sm">
                        {{ $transfer->toBranch->address ?? '-' }}<br>
                        Tel: {{ $transfer->toBranch->phone ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if($transfer->notes)
        <div class="px-8 py-4 bg-white border-b border-gray-200">
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1">Catatan</p>
            <p class="text-sm text-slate-700 italic">{{ $transfer->notes }}</p>
        </div>
        @endif

        <div class="p-0 sm:p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-500 font-bold text-[11px] uppercase tracking-widest border-b border-gray-200">
                            <th class="px-4 py-4 text-left">Nama Bahan Baku</th>
                            <th class="px-4 py-4 text-center">Jumlah Dikirim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($transfer->items as $item)
                        <tr>
                            <td class="px-4 py-5 font-bold text-black">
                                {{ $item->ingredient->name ?? 'Unknown Item' }}
                            </td>
                            <td class="px-4 py-5 text-center font-bold text-black">
                                {{ floatval($item->quantity) }} 
                                <span class="text-[10px] font-medium text-gray-500">{{ $item->ingredient->unit->name ?? 'pcs' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="p-8 border-t border-gray-200 flex justify-between items-center bg-white">
            <div>
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Dibuat Oleh</p>
                <p class="text-sm font-semibold text-slate-700 mt-1">{{ $transfer->user->name ?? 'Sistem' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Status</p>
                <p class="text-black font-bold mt-1 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:check-circle"></span> Selesai
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
