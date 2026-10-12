@extends('layouts.admin')

@section('title', 'KasPro Intelligence Dashboard')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .dashboard-wrapper { font-family: 'Inter', sans-serif; background-color: #f9fafb; letter-spacing: -0.01em; }
    .card-premium { background: white; border: 1px solid #edf2f7; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .text-numeric { font-variant-numeric: tabular-nums; }
</style>

<div class="dashboard-wrapper min-h-screen p-6 md:p-8 space-y-8">
    
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold text-black tracking-tight">KasPro Intelligence</h1>
            <p class="text-gray-500 text-sm font-medium">Monitor dan prediksi kesehatan keuangan bisnis Anda</p>
        </div>
        <div class="flex gap-3 items-center">
             <form action="{{ route('kaspro.dashboard') }}" method="GET" class="flex gap-2 items-center">
                 <input type="date" name="start_date" class="px-3 py-1.5 text-xs border border-gray-200 rounded-md text-gray-600 focus:outline-none focus:border-gray-200 focus:ring-1 focus:ring-black/5" value="{{ request('start_date', \Carbon\Carbon::createFromFormat('d/m/Y', $cashFlow['start_date'])->format('Y-m-d')) }}">
                 <span class="text-xs text-gray-500 font-medium">s/d</span>
                 <input type="date" name="end_date" class="px-3 py-1.5 text-xs border border-gray-200 rounded-md text-gray-600 focus:outline-none focus:border-gray-200 focus:ring-1 focus:ring-black/5" value="{{ request('end_date', \Carbon\Carbon::createFromFormat('d/m/Y', $cashFlow['end_date'])->format('Y-m-d')) }}">
                 <button type="submit" class="px-3 py-1.5 bg-black text-white text-white text-xs font-bold rounded-md shadow-sm hover:bg-gray-800 transition-colors">Filter</button>
             </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card-premium p-6 lg:col-span-2">
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Saldo Kas Saat Ini</p>
                    <h2 class="text-4xl font-semibold text-black text-numeric">{{ $survival['current_cash_formatted'] }}</h2>
                    <p class="text-sm text-gray-500 pt-2">
                        Rata-rata pengeluaran harian: <span class="font-bold text-slate-700">{{ $survival['avg_daily_expense_formatted'] }}</span>
                    </p>
                </div>
                <div class="bg-white rounded-md p-5 border border-gray-200 min-w-[240px]">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-[11px] font-bold text-gray-500 uppercase">Estimasi Runway</p>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-50 border border-gray-200 text-gray-700 uppercase">
                            {{ $survival['status'] }}
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-semibold text-black">{{ $survival['days_to_survive'] }}</span>
                        <span class="text-sm font-bold text-gray-500">Hari Bertahan</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium p-6">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-[10px] font-bold uppercase mb-1 text-black">Pemasukan</p>
                    <p class="text-lg font-semibold text-black">Rp {{ number_format($cashFlow['total_income'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase mb-1 text-rose-600">Pengeluaran</p>
                    <p class="text-lg font-semibold text-black">Rp {{ number_format($cashFlow['total_expense'], 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between text-[10px] font-bold text-gray-500">
                    <span>STOK: Rp {{ number_format($cashFlow['expense_breakdown']['stok'], 0, ',', '.') }}</span>
                    <span>OPS: Rp {{ number_format($cashFlow['expense_breakdown']['operasional'], 0, ',', '.') }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-md h-2 flex overflow-hidden">
                    @php
                        $totalExp = $cashFlow['total_expense'] ?: 1;
                        $stokPct = ($cashFlow['expense_breakdown']['stok'] / $totalExp) * 100;
                    @endphp
                    <div class="bg-black h-full" style="width: {{ $stokPct }}%"></div>
                    <div class="bg-slate-300 h-full" style="width: {{ 100 - $stokPct }}%"></div>
                </div>
                <div class="pt-4 border-t border-slate-50 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-500">NET CASH FLOW:</span>
                    <span class="text-lg font-semibold {{ $cashFlow['net_cash_flow'] >= 0 ? 'text-black' : 'text-rose-600' }}">
                        Rp {{ number_format($cashFlow['net_cash_flow'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card-premium p-8 min-w-0 overflow-hidden">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-base font-semibold text-black italic">Historical Flow (12 Months)</h3>
            <div class="flex gap-4">
                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-500">
                    <span class="w-3 h-3 rounded-md bg-white border border-gray-2000"></span> INFLOW
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-500">
                    <span class="w-3 h-3 rounded-md bg-rose-500"></span> OUTFLOW
                </div>
            </div>
        </div>
        <div id="cashFlowChart" class="w-full" style="min-height: 350px;"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 card-premium overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="text-sm font-bold text-black uppercase tracking-wider">Prediksi 3 Bulan Mendatang</h3>
                @if(isset($prediction['growth_rate']))
                    <span class="text-[10px] font-bold text-black bg-gray-50 px-2 py-1 rounded">GROWTH: {{ number_format(($prediction['growth_rate'] - 1) * 100, 1) }}%</span>
                @endif
            </div>
            <div class="p-6">
                @if(!empty($prediction['predictions']))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($prediction['predictions'] as $pred)
                    <div class="border border-gray-200 rounded-md p-4 bg-gray-50 hover:shadow-sm transition">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-3">{{ $pred['month_name'] }}</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>Pemasukan</span>
                                <span class="text-black font-bold">Rp{{ number_format($pred['predicted_income']/1000,0) }}k</span>
                            </div>
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>Pengeluaran</span>
                                <span class="text-rose-500 font-bold">Rp{{ number_format($pred['predicted_expense']/1000,0) }}k</span>
                            </div>
                            <div class="pt-2 border-t border-slate-50 flex justify-between items-baseline">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Net</span>
                                <span class="text-md font-semibold {{ $pred['predicted_net'] >= 0 ? 'text-black' : 'text-rose-600' }}">
                                    Rp{{ number_format($pred['predicted_net'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-center py-10 text-gray-500 text-sm italic">Data historis tidak mencukupi untuk prediksi AI.</p>
                @endif
                
                @if(!empty($prediction['recommendations']))
                <div class="mt-6 p-4 bg-gray-50 rounded-md border border-gray-200 flex gap-4 items-start">
                    <span class="text-xl">ðŸ’¡</span>
                    <div>
                        <h4 class="text-xs font-bold text-black uppercase mb-1 tracking-widest">KasPro Strategy Insight</h4>
                        @foreach($prediction['recommendations'] as $rec)
                            <p class="text-sm text-amber-900/80 leading-relaxed">â€¢ {{ $rec }}</p>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-4 card-premium flex flex-col">
            <div class="px-4 py-2 border-b border-gray-200 bg-white">
                <h3 class="text-sm font-bold text-black uppercase tracking-wider">Peringatan Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-200 overflow-y-auto max-h-[400px]">
                @forelse($recentAlerts as $alert)
                <div class="p-4 hover:bg-white transition group">
                    <div class="flex justify-between items-start mb-1">
                        @php
                            $colors = [
                                'emergency' => 'bg-red-100 text-red-700',
                                'critical' => 'bg-orange-100 text-orange-700',
                                'warning' => 'bg-gray-50 text-black',
                                'info' => 'bg-gray-50 border border-gray-200 text-black'
                            ];
                            $color = $colors[$alert->severity] ?? $colors['info'];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $color }}">
                            {{ $alert->severity }}
                        </span>
                        <span class="text-[10px] font-medium text-gray-500">{{ $alert->created_at->diffForHumans() }}</span>
                    </div>
                    <h5 class="text-sm font-bold text-black group-hover:text-black transition">{{ $alert->title }}</h5>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $alert->message }}</p>
                </div>
                @empty
                <p class="p-8 text-center text-gray-500 text-sm italic">Tidak ada peringatan sistem.</p>
                @endforelse
            </div>
            <a href="{{ route('kaspro.alerts') }}" class="mt-auto p-4 text-center border-t border-gray-200 text-xs font-bold text-black hover:bg-gray-50 transition">
                LIHAT SEMUA NOTIFIKASI â†’
            </a>
        </div>
    </div>
</div>

<script>
    function initCashFlowChart() {
        console.log("Initializing KasPro Chart...");
        const chartContainer = document.querySelector("#cashFlowChart");
        
        // Cek jika elemen ada di DOM
        if (!chartContainer) return;

        // Bersihkan konten sebelumnya agar tidak double render saat navigasi Turbo
        chartContainer.innerHTML = '';

        const months = @json($historicalData['months'] ?? []);
        
        if (typeof ApexCharts !== 'undefined' && months.length > 0) {
            const options = {
                series: [
                    { name: 'Pemasukan', data: @json($historicalData['income'] ?? []) },
                    { name: 'Pengeluaran', data: @json($historicalData['expense'] ?? []) }
                ],
                chart: { 
                    type: 'area', 
                    height: 350, 
                    width: '100%',
                    toolbar: { show: false }, 
                    fontFamily: 'Inter, sans-serif',
                    animations: { enabled: true }
                },
                colors: ['#000000', '#9ca3af'],
                stroke: { curve: 'smooth', width: 2 },
                fill: { 
                    type: 'solid',
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: months, 
                    axisBorder: { show: false }, 
                    labels: { style: { colors: '#94a3b8', fontWeight: 600 } } 
                },
                yaxis: { 
                    labels: { 
                        formatter: (v) => 'Rp' + (v/1000000).toFixed(1) + 'jt', 
                        style: { colors: '#94a3b8' } 
                    } 
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { show: false },
                tooltip: { 
                    theme: 'light', 
                    x: { show: true }, 
                    y: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } 
                }
            };

            const chart = new ApexCharts(chartContainer, options);
            chart.render();
        }
    }

    window.renderWhenReady = function() {
        if (typeof ApexCharts !== 'undefined') {
            const chartContainer = document.querySelector("#cashFlowChart");
            if (chartContainer && chartContainer.offsetWidth === 0) {
                setTimeout(window.renderWhenReady, 50);
                return;
            }
            initCashFlowChart();
        } else {
            setTimeout(window.renderWhenReady, 50);
        }
    }
    window.renderWhenReady();
</script>
@endsection
