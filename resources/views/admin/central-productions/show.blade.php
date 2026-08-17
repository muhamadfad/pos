@extends('layouts.admin')

@section('title', 'Detail Produksi (Olah Bahan)')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.central-productions.index') }}" 
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                <span class="iconify" data-icon="lucide:arrow-left" data-width="24"></span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    Detail Produksi
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg border border-slate-200">
                        {{ $production->reference_number }}
                    </span>
                </h1>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Hasil Produksi -->
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100 shadow-sm relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-blue-100/50">
                <span class="iconify" data-icon="lucide:package-check" data-width="120"></span>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-6 h-6 rounded bg-blue-100 flex items-center justify-center mr-2">
                        <span class="iconify" data-icon="lucide:check" data-width="14"></span>
                    </span>
                    Barang Jadi (Hasil)
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $production->resultIngredient?->name ?? 'Bahan Dihapus' }}</h3>
                <div class="text-3xl font-black text-blue-600 mb-2">
                    {{ number_format($production->result_quantity, 1, ',', '.') }} <span class="text-lg text-blue-400 font-semibold">{{ $production->resultIngredient?->unit?->name ?? 'kg' }}</span>
                </div>
                <p class="text-sm text-slate-500 flex items-center">
                    <span class="iconify mr-1.5" data-icon="lucide:calendar" data-width="14"></span>
                    {{ \Carbon\Carbon::parse($production->date)->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        <!-- Biaya Modal -->
        <div class="bg-gradient-to-br from-orange-50 to-white rounded-2xl p-6 border border-orange-100 shadow-sm relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-orange-100/50">
                <span class="iconify" data-icon="lucide:coins" data-width="120"></span>
            </div>
            <div class="relative z-10">
                <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-6 h-6 rounded bg-orange-100 flex items-center justify-center mr-2">
                        <span class="iconify" data-icon="lucide:wallet" data-width="14"></span>
                    </span>
                    Modal Bahan Baku
                </div>
                <div class="text-sm text-slate-500 mb-1">Total Biaya Bahan Mentah</div>
                <div class="text-3xl font-black text-orange-600 mb-4">
                    Rp {{ number_format($production->total_cost, 0, ',', '.') }}
                </div>
                
                <div class="pt-4 border-t border-orange-200/50">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">HPP (Modal) per {{ $production->resultIngredient?->unit?->name ?? 'kg' }}:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($production->total_cost / ($production->result_quantity > 0 ? $production->result_quantity : 1), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden mb-6">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 flex items-center">
                <span class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mr-3">
                    <span class="iconify" data-icon="lucide:flame" data-width="16"></span>
                </span>
                Rincian Bahan Mentah yang Dipakai
            </h3>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg border border-slate-200">
                {{ $production->items->count() }} Bahan
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/30 text-xs uppercase font-semibold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Bahan Baku</th>
                        <th class="px-6 py-4 text-right">Harga Satuan</th>
                        <th class="px-6 py-4 text-right">Kuantitas Dipakai</th>
                        <th class="px-6 py-4 text-right">Subtotal Modal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($production->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $item->ingredient->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-medium text-xs">
                                    {{ $item->quantity }} {{ $item->ingredient->unit->name ?? 'kg' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-800">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/80 font-bold border-t-2 border-slate-200">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-slate-600">Total Biaya Bahan Mentah:</td>
                        <td class="px-6 py-4 text-right text-orange-600 text-base">
                            Rp {{ number_format($production->total_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($production->notes)
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 mb-6">
            <h4 class="text-sm font-bold text-yellow-800 flex items-center mb-2">
                <span class="iconify mr-2" data-icon="lucide:sticky-note" data-width="16"></span>
                Catatan Produksi
            </h4>
            <p class="text-sm text-yellow-700 leading-relaxed">
                {{ $production->notes }}
            </p>
        </div>
    @endif
    
</div>
@endsection
