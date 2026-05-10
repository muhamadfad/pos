@extends('layouts.admin')

@section('title', 'Detail Resep')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="p-2 lg:p-4 max-w-[1200px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">
    
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <span>Inventory</span>
                <span class="mx-2">/</span>
                <span>Recipes</span>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">{{ $product->name }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Detail Resep</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $product->name }}</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recipes.edit', $product->id) }}" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                Edit Resep
            </a>
            <a href="{{ route('admin.recipes.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                <span class="iconify text-lg" data-icon="lucide:arrow-left"></span>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Komposisi Bahan Baku -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                        <span class="iconify text-indigo-500" data-icon="lucide:package"></span>
                        Komposisi Bahan Baku
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200">
                                <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Bahan Baku</th>
                                <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Jumlah</th>
                                <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Satuan</th>
                                <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Harga / Unit</th>
                                <th class="px-6 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php $totalHPP = 0; @endphp
                            @foreach($product->recipeItems as $item)
                            @php
                                $unitCost = $item->ingredient->unit_cost ?? 0;
                                $subtotal = $item->quantity * $unitCost;
                                $totalHPP += $subtotal;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-800">{{ $item->ingredient->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm text-slate-600">{{ number_format($item->quantity, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm text-slate-600">{{ $item->unit->name ?? $item->ingredient->unit->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm text-slate-600">Rp {{ number_format($unitCost, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-medium text-slate-700">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-semibold text-slate-700">Total HPP:</td>
                                <td class="px-6 py-4 text-right font-bold text-rose-600">Rp {{ number_format($totalHPP, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ringkasan Finansial -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span class="iconify" data-icon="lucide:trending-up"></span>
                        Ringkasan Finansial
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Harga Jual</span>
                        <span class="text-xl font-bold text-slate-800">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Total HPP</span>
                        <span class="text-xl font-bold text-rose-600">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-dashed border-slate-200">
                        <span class="text-sm font-semibold text-slate-600">Margin Kotor</span>
                        @php
                            $margin = $product->selling_price - $totalHPP;
                            $marginPercent = $totalHPP > 0 ? ($margin / $totalHPP) * 100 : 0;
                            $marginColor = $marginPercent >= 50 ? 'text-emerald-600' : ($marginPercent >= 30 ? 'text-blue-600' : ($marginPercent >= 15 ? 'text-amber-600' : 'text-rose-600'));
                        @endphp
                        <div class="text-right">
                            <span class="text-xl font-bold {{ $marginColor }}">Rp {{ number_format($margin, 0, ',', '.') }}</span>
                            <span class="text-xs {{ $marginColor }} ml-1">({{ number_format($marginPercent, 1) }}%)</span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar Margin -->
                    <div class="pt-2">
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Margin %</span>
                            <span>{{ number_format($marginPercent, 1) }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ min($marginPercent, 100) }}%"></div>
                        </div>
                        <div class="grid grid-cols-4 gap-1 mt-2 text-[10px] text-slate-400">
                            <span class="text-center">0%</span>
                            <span class="text-center">25%</span>
                            <span class="text-center">50%</span>
                            <span class="text-center">75%</span>
                            <span class="text-center">100%</span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mt-4 pt-4 border-t border-slate-200">
                        @php
                            $statusClass = $marginPercent >= 50 ? 'bg-emerald-100 text-emerald-700' : 
                                          ($marginPercent >= 30 ? 'bg-blue-100 text-blue-700' : 
                                          ($marginPercent >= 15 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700'));
                            $statusText = $marginPercent >= 50 ? 'Sangat Menguntungkan' : 
                                         ($marginPercent >= 30 ? 'Menguntungkan' : 
                                         ($marginPercent >= 15 ? 'Cukup' : 'Margin Rendah'));
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Status Margin:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>

                    <!-- Tips / Rekomendasi -->
                    @if($marginPercent < 30)
                    <div class="mt-4 p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="flex items-start gap-2">
                            <span class="iconify text-amber-500 text-sm" data-icon="lucide:lightbulb"></span>
                            <div class="text-xs">
                                <p class="font-semibold text-amber-700 mb-1">Rekomendasi:</p>
                                <p class="text-amber-600">
                                    @if($marginPercent < 15)
                                        Margin terlalu rendah. Pertimbangkan untuk menaikkan harga jual atau mencari supplier dengan harga lebih murah.
                                    @else
                                        Margin masih bisa ditingkatkan. Coba evaluasi harga jual atau efisiensi bahan baku.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Info Harga Bahan -->
                    <div class="mt-4 pt-4 border-t border-slate-200">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <span class="iconify" data-icon="lucide:info"></span>
                            <span>Harga bahan baku dihitung berdasarkan harga beli terakhir</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tooltip untuk progress bar (opsional)
    document.querySelectorAll('[data-tooltip]').forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            // Fungsi tooltip jika diperlukan
        });
    });
</script>
@endsection