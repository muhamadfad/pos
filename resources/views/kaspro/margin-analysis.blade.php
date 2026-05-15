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
    
    <!-- Header dengan Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Product Margin Analysis</h1>
            <p class="text-slate-500 text-sm font-medium">Evaluasi profitabilitas produk berdasarkan HPP dari bahan baku.</p>
        </div>
        <div class="flex items-center gap-3">
            @if($isSuperAdmin ?? false)
            <select id="branchFilter" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold">
                <option value="">🌍 Semua Cabang</option>
                @foreach($branches ?? [] as $branch)
                <option value="{{ $branch->id }}" {{ ($branchId ?? '') == $branch->id ? 'selected' : '' }}>
                    🏪 {{ $branch->name }}
                </option>
                @endforeach
            </select>
            @endif
            
            <select id="periodFilter" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold">
                <option value="daily" {{ ($period ?? 'monthly') == 'daily' ? 'selected' : '' }}>Harian</option>
                <option value="weekly" {{ ($period ?? 'monthly') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                <option value="monthly" {{ ($period ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                <option value="yearly" {{ ($period ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                <option value="all" {{ ($period ?? 'monthly') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
            </select>
            
            <button onclick="applyFilters()" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold">
                Terapkan
            </button>
        </div>
    </div>
    
    <!-- Info Periode -->
    <div class="bg-indigo-50 rounded-2xl p-3 px-5">
        <div class="flex items-center gap-4">
            <span class="iconify text-indigo-600" data-icon="mdi:store"></span>
            <span class="text-sm font-semibold text-indigo-800">Cabang: {{ $currentBranchName ?? 'Semua Cabang' }}</span>
            <div class="w-px h-4 bg-indigo-200"></div>
            <span class="iconify text-indigo-600" data-icon="mdi:calendar-range"></span>
            <span class="text-sm font-semibold text-indigo-800">Periode: {{ $dateRange['label'] ?? 'Bulan Ini' }}</span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase">Total Revenue</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase">Total HPP</p>
            <p class="text-2xl font-black text-amber-600 mt-1">Rp {{ number_format($TotalHPP ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase">Total Profit</p>
            <p class="text-2xl font-black text-indigo-600 mt-1">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-5 text-white">
            <p class="text-[10px] font-black text-white/70 uppercase">Overall Margin</p>
            <p class="text-2xl font-black mt-1">{{ number_format($overallMargin ?? 0, 1) }}%</p>
        </div>
    </div>

    <!-- Low Margin Warning -->
    @if(isset($lowMarginProducts) && count($lowMarginProducts) > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="iconify text-amber-600 text-xl" data-icon="mdi:alert-octagon"></span>
            <h3 class="font-bold text-amber-800 text-sm">⚠️ Produk dengan Margin Rendah (&lt; 20%)</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($lowMarginProducts as $product)
            <div class="bg-white rounded-xl p-3 border border-amber-100">
                <div class="font-semibold text-amber-800">{{ $product->product_name }}</div>
                <div class="flex justify-between text-xs mt-2">
                    <span>Margin: {{ number_format($product->margin, 1) }}%</span>
                    <span>Profit: Rp {{ number_format($product->profit, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Main Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 table-header">Informasi Produk</th>
                        <th class="px-6 py-4 table-header text-right">Volume</th>
                        <th class="px-6 py-4 table-header text-right">Gross Revenue</th>
                        <th class="px-6 py-4 table-header text-right">HPP</th>
                        <th class="px-6 py-4 table-header text-right">Est. Margin</th>
                        <th class="px-6 py-4 table-header text-center">Profitability</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($productMargins ?? [] as $item)
                    @php
                        $marginPercent = $item->margin;
                        $statusColor = $marginPercent >= 30 ? 'emerald' : ($marginPercent >= 15 ? 'amber' : 'rose');
                        $statusText = $marginPercent >= 30 ? 'High Margin' : ($marginPercent >= 15 ? 'Fair' : 'Thin');
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 group-hover:text-indigo-600 transition">{{ $item->product_name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">Harga Jual: Rp {{ number_format($item->selling_price, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-600 text-numeric">
                            {{ number_format($item->total_quantity, 0, ',', '.') }} <span class="text-[10px] text-slate-400">PCS</span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900 text-numeric text-sm">
                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-slate-600">
                            Rp {{ number_format($item->hpp, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="font-bold text-{{ $statusColor }}-600 text-numeric">Rp {{ number_format($item->profit, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-bold text-{{ $statusColor }}-500">{{ number_format($marginPercent, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 border border-{{ $statusColor }}-100">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
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

    <!-- Strategic Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-indigo-900 rounded-[28px] p-8 text-white relative overflow-hidden shadow-xl">
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

<script>
    function applyFilters() {
        const branchId = document.getElementById('branchFilter')?.value || '';
        const period = document.getElementById('periodFilter')?.value || 'monthly';
        const url = new URL(window.location.href);
        
        if (branchId) {
            url.searchParams.set('branch_id', branchId);
        } else {
            url.searchParams.delete('branch_id');
        }
        
        url.searchParams.set('period', period);
        window.location.href = url.toString();
    }
    
    document.getElementById('periodFilter')?.addEventListener('change', applyFilters);
    document.getElementById('branchFilter')?.addEventListener('change', applyFilters);
</script>
@endsection