@extends('layouts.admin')

@section('title', 'Analisis Margin Produk')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .margin-wrapper { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
    .glass-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .text-numeric { font-variant-numeric: tabular-nums; }
    .table-header { text-transform: uppercase; letter-spacing: 0.1em; font-size: 10px; font-weight: 700; color: #94a3b8; }
</style>

<div class="margin-wrapper min-h-screen p-6 md:p-8 space-y-8">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Product Margin Analysis</h1>
            <p class="text-slate-500 text-sm font-medium">Evaluasi profitabilitas 10 produk dengan volume tertinggi.</p>
        </div>
        <div class="px-4 py-2 bg-indigo-50 rounded-xl border border-indigo-100">
            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest block">Update Terbaru</span>
            <span class="text-xs font-bold text-slate-700">{{ now()->format('d M Y, H:i') }}</span>
        </div>
    </div>

    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 table-header">Informasi Produk</th>
                        <th class="px-8 py-4 table-header text-right">Volume</th>
                        <th class="px-8 py-4 table-header text-right">Gross Revenue</th>
                        <th class="px-8 py-4 table-header text-right">Est. Margin</th>
                        <th class="px-8 py-4 table-header text-center">Profitability</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($topProducts as $item)
                    @php
                        $avgCost = $item->purchase_price ?? 0;
                        $estimatedMargin = $item->total_revenue - ($avgCost * $item->total_sold);
                        $marginPercent = $item->total_revenue > 0 ? ($estimatedMargin / $item->total_revenue) * 100 : 0;
                        
                        // Penentuan Warna Badge
                        $statusColor = $marginPercent >= 30 ? 'emerald' : ($marginPercent >= 15 ? 'amber' : 'rose');
                        $statusText = $marginPercent >= 30 ? 'High Margin' : ($marginPercent >= 15 ? 'Fair' : 'Thin');
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 group-hover:text-indigo-600 transition">{{ $item->product_name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">HPP: Rp{{ number_format($avgCost, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right font-semibold text-slate-600 text-numeric">
                            {{ number_format($item->total_sold, 0, ',', '.') }} <span class="text-[10px] text-slate-400">PCS</span>
                        </td>
                        <td class="px-8 py-5 text-right font-bold text-slate-900 text-numeric text-sm">
                            Rp{{ number_format($item->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex flex-col items-end">
                                <span class="font-bold text-{{ $statusColor }}-600 text-numeric">Rp{{ number_format($estimatedMargin, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-bold text-{{ $statusColor }}-500">{{ number_format($marginPercent, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 border border-{{ $statusColor }}-100">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-4xl opacity-20">📊</span>
                                <p class="mt-4 text-slate-400 font-medium italic">Belum ada data penjualan tercatat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-indigo-900 rounded-[28px] p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-100">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">💡</span>
                    <h3 class="text-xs font-black uppercase tracking-widest opacity-60">Strategic Strategy</h3>
                </div>
                <p class="text-sm font-medium leading-relaxed italic text-indigo-50">
                    "Fokuskan alokasi stok dan campaign pemasaran pada produk dengan label <strong>'High Margin'</strong> untuk mempercepat pertumbuhan kas tanpa meningkatkan beban operasional secara signifikan."
                </p>
            </div>
            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl"></div>
        </div>

        <div class="glass-card p-8 border-l-4 border-amber-500">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Health Check</h3>
            <p class="text-sm text-slate-600 font-medium leading-relaxed">
                Produk dengan margin di bawah <span class="text-rose-500 font-bold">15% (Thin)</span> memerlukan evaluasi harga jual atau negosiasi ulang biaya pengadaan ke supplier agar tidak menggerus profit bersih.
            </p>
        </div>
    </div>
</div>
@endsection