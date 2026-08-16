@extends('layouts.admin')

@extends('layouts.admin')

@section('title', 'Laporan Harian')

@section('content')
<div class="space-y-8">

    <!-- Branding & Range -->
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <span class="iconify text-white text-2xl" data-icon="mdi:calendar-today"></span>
                </div>
                Laporan Harian
            </h2>
            <p class="text-slate-500 font-medium mt-2">
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                @elseif($startDate)
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                @else
                    {{ \Carbon\Carbon::today()->translatedFormat('d M Y') }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Date Filter -->
            <form action="{{ route('admin.finance.daily') }}" method="GET" class="flex gap-2 items-center">
                <input type="date" name="start_date" class="px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-600 focus:ring-2 focus:ring-indigo-500" value="{{ $startDate }}">
                <span class="text-xs text-slate-500">s/d</span>
                <input type="date" name="end_date" class="px-3 py-2 text-xs border border-slate-200 rounded-xl text-slate-600 focus:ring-2 focus:ring-indigo-500" value="{{ $endDate }}">
                @if(request('branch_id'))
                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                @endif
                @if(request('payment_method'))
                    <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">

    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase text-sm">Produk Terlaris</h3>
                <span class="iconify text-slate-300 text-xl" data-icon="mdi:chart-bar"></span>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl w-fit">
                <a href="{{ request()->fullUrlWithQuery(['payment_method' => null]) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ !($paymentMethodFilter ?? null) ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Semua</a>
                <a href="{{ request()->fullUrlWithQuery(['payment_method' => 'cash']) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ ($paymentMethodFilter ?? null) === 'cash' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Cash</a>
                <a href="{{ request()->fullUrlWithQuery(['payment_method' => 'transfer']) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ ($paymentMethodFilter ?? null) === 'transfer' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Transfer</a>
            </div>
        </div>
        <div class="h-[400px]">
            @if($topProducts->count() > 0)
            <canvas id="dailyChart"></canvas>
            @else
            <div class="flex flex-col items-center justify-center h-full text-slate-400">
                <span class="iconify text-5xl mb-2" data-icon="mdi:database-off"></span>
                <p class="text-[10px] font-black uppercase tracking-widest">Tidak ada data penjualan</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Daftar Pengeluaran Hari Ini -->
    @if(isset($expenses) && $expenses->count() > 0)
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-black text-slate-800 flex items-center gap-2">
                <span class="iconify text-rose-500" data-icon="mdi:cash-minus"></span>
                Detail Pengeluaran Hari Ini
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4 text-xs font-black text-slate-500 uppercase">Kategori</th>
                        <th class="text-left py-3 px-4 text-xs font-black text-slate-500 uppercase">Deskripsi</th>
                        <th class="text-left py-3 px-4 text-xs font-black text-slate-500 uppercase">Dicatat Oleh</th>
                        <th class="text-right py-3 px-4 text-xs font-black text-slate-500 uppercase">Jumlah</th>
                        <th class="text-left py-3 px-4 text-xs font-black text-slate-500 uppercase">Referensi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr class="border-b border-slate-100">
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-rose-50 text-rose-600 text-xs font-medium rounded-full">{{ $expense->category }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $expense->description }}</td>
                        <td class="py-3 px-4 text-slate-600 font-semibold">{{ $expense->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-right font-mono font-semibold text-rose-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-slate-500 text-sm">{{ $expense->reference ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="3" class="py-3 px-4 font-bold text-slate-800">Total Pengeluaran</td>
                        <td class="py-3 px-4 text-right font-bold text-rose-600">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Branch filter
    document.getElementById('branchFilter')?.addEventListener('change', function() {
        const branchId = this.value;
        const url = new URL(window.location.href);
        if (branchId) {
            url.searchParams.set('branch_id', branchId);
        } else {
            url.searchParams.delete('branch_id');
        }
        window.location.href = url.toString();
    });
    
    @if($topProducts->count() > 0)
    window.renderWhenReady = function() {
        const canvas = document.getElementById('dailyChart');
        if (!canvas) return;
        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProducts->pluck('name')) !!},
                datasets: [{
                    label: 'Qty Terjual',
                    data: {!! json_encode($topProducts->pluck('total_qty')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 12,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 10, weight: 'bold' },
                        bodyFont: { size: 12, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { weight: 'bold', size: 11 } }
                    },
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { weight: 'bold', size: 10 } }
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
    @endif
</script>
@endpush
@endsection
