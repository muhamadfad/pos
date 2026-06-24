@extends('layouts.admin')

@section('title', 'Prediksi Arus Kas')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .prediction-wrapper { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
    .glass-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
    .text-numeric { font-variant-numeric: tabular-nums; }
    /* Fix untuk chart agar tidak overflow */
    .chart-container { position: relative; height: 300px; width: 100%; }
</style>

<div class="prediction-wrapper min-h-screen p-6 md:p-8 space-y-8">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Intelligence Forecast</h1>
            <p class="text-slate-500 text-sm font-medium">Proyeksi arus kas berbasis performa historis.</p>
        </div>
        
        @if(isset($prediction['growth_rate']))
        <div class="flex items-center gap-4 px-6 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="flex flex-col">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1 text-right">Growth Rate</p>
                <p class="text-xl font-black text-slate-900 leading-none">
                    {{ number_format(($prediction['growth_rate'] - 1) * 100, 1) }}%
                </p>
            </div>
            <div class="h-8 w-[1px] bg-slate-100"></div>
            <span class="text-xs font-bold {{ $prediction['growth_rate'] >= 1 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $prediction['growth_rate'] >= 1 ? 'PROFITABLE ▲' : 'DECLINING ▼' }}
            </span>
        </div>
        @endif
    </div>

    <div class="glass-card overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider text-numeric">Detail Proyeksi Bulanan</h2>
        </div>

        <div class="overflow-x-auto">
            @if(!empty($prediction['predictions']))
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bulan</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Est. Masuk</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Est. Keluar</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Net Flow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($prediction['predictions'] as $pred)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-8 py-4">
                            <span class="font-bold text-slate-800">{{ $pred['month_name'] }}</span>
                            @if(isset($pred['seasonal_factor']) && $pred['seasonal_factor'] > 1)
                                <span class="ml-2 text-[9px] font-black bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded uppercase">Peak</span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-emerald-600 font-medium text-numeric text-sm">
                            Rp{{ number_format($pred['predicted_income'], 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-4 text-rose-500 font-medium text-numeric text-sm">
                            Rp{{ number_format($pred['predicted_expense'], 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-4 text-right">
                            <span class="text-sm font-bold text-numeric {{ $pred['predicted_net'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $pred['predicted_net'] >= 0 ? '+' : '' }}Rp{{ number_format($pred['predicted_net'], 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-12 text-center text-slate-400 italic text-sm">Data tidak tersedia.</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 glass-card p-8">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest mb-6">Visualisasi Tren Historis</h3>
            <div class="chart-container">
                <canvas id="historicalChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="glass-card p-6 border-l-4 border-emerald-500">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Avg. Predicted Income</h4>
                <p class="text-2xl font-black text-slate-900 text-numeric">
                    @php
                        $avgIncome = count($prediction['predictions']) > 0 
                            ? collect($prediction['predictions'])->avg('predicted_income') 
                            : 0;
                    @endphp
                    Rp{{ number_format($avgIncome, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1 italic">*Rata-rata pemasukan per bulan di periode depan</p>
            </div>

            @if(!empty($prediction['recommendations']))
            <div class="bg-slate-900 rounded-[24px] p-6 text-white shadow-xl">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-amber-400 font-bold">💡</span>
                    <h4 class="text-[10px] font-bold uppercase tracking-widest opacity-60 text-white">AI Strategy Insight</h4>
                </div>
                <ul class="space-y-3">
                    @foreach($prediction['recommendations'] as $rec)
                    <li class="text-xs font-medium leading-relaxed border-l border-slate-700 pl-3">
                        {{ $rec }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    window.historicalData = {
        months: @json($historicalData['months'] ?? []),
        income: @json($historicalData['income'] ?? []),
        expense: @json($historicalData['expense'] ?? [])
    };
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.renderWhenReady = function() {
        const canvas = document.getElementById('historicalChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // Cek jika data kosong
        if(window.historicalData.months.length === 0) return;

        // Destroy chart lama jika ada di canvas ini
        if (window.predictionHistoricalChart) {
            window.predictionHistoricalChart.destroy();
        }

        window.predictionHistoricalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.historicalData.months,
                datasets: [
                    {
                        label: 'Income',
                        data: window.historicalData.income,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981'
                    },
                    {
                        label: 'Expense',
                        data: window.historicalData.expense,
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#f43f5e'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: {
                            callback: v => 'Rp' + (v/1000000).toFixed(0) + 'jt',
                            font: { size: 10, weight: '600' },
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8' }
                    }
                }
            }
        });
    }
    window.checkLib = function() {
        if(typeof Chart !== 'undefined') {
            // Find canvas
            const canvases = document.getElementsByTagName('canvas');
            if (canvases.length > 0 && canvases[0].offsetWidth === 0) {
                setTimeout(window.checkLib, 50);
                return;
            }
            window.renderWhenReady(); 
        } else { 
            setTimeout(window.checkLib, 50); 
        } 
    };
    window.checkLib();
</script>
@endpush
@endsection
