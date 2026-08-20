@extends('layouts.admin')

@section('title', 'Laporan Mingguan')

@section('content')
<div class="space-y-8">

    <!-- Branding & Range -->
    <div class="flex flex-col md:flex-row flex-wrap md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:calendar-week"></span>
                </span>
                Laporan Mingguan
            </h1>
            <p class="text-slate-500 font-medium">{{ $start->translatedFormat('d M Y') }} - {{ $end->translatedFormat('d M Y') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Branch Filter (Hanya untuk Super Admin) -->
            @if($isSuperAdmin ?? false)
            <select id="branchFilter" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Cabang</option>
                @foreach($branches ?? [] as $branch)
                <option value="{{ $branch->id }}" {{ ($branchId ?? '') == $branch->id ? 'selected' : '' }}>
                    <span class="iconify inline-block" data-icon="mdi:store-outline"></span> {{ $branch->name }}
                </option>
                @endforeach
            </select>
            @endif
            
            
            <a href="{{ route('admin.finance.export', ['period' => 'weekly', 'branch_id' => $branchId]) }}" class="whitespace-nowrap flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl text-sm font-bold hover:bg-red-100 transition-all">
                <span class="iconify" data-icon="mdi:file-pdf-box"></span> Export PDF
            </a>
            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl">
                <a href="{{ route('admin.finance.index') . ($branchId ? '?branch_id=' . $branchId : '') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.index') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Ringkasan</a>
                <a href="{{ route('admin.finance.daily') . ($branchId ? '?branch_id=' . $branchId : '') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.daily') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Harian</a>
                <a href="{{ route('admin.finance.weekly') . ($branchId ? '?branch_id=' . $branchId : '') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.weekly') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Mingguan</a>
                <a href="{{ route('admin.finance.monthly') . ($branchId ? '?branch_id=' . $branchId : '') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.monthly') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Bulanan</a>
            </div>
        </div>
    </div>
    
    <!-- Info Cabang -->
    @if(isset($currentBranchName))
    <div class="bg-indigo-50 rounded-2xl p-3 px-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="iconify text-indigo-600" data-icon="mdi:store"></span>
            <span class="text-sm font-semibold text-indigo-800">Cabang: {{ $currentBranchName }}</span>
        </div>
        @if(($isSuperAdmin ?? false) && ($branchId ?? false))
        <a href="{{ route('admin.finance.weekly') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Reset Filter</a>
        @endif
    </div>
    @endif

    <!-- Stats Grid - 4 kolom untuk card utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Income Card -->
        <div class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Total Pendapatan</p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-emerald-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($finance['income'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:trending-up"></span>
                Penjualan Bersih
            </div>
        </div>

        <!-- HPP Card -->
        <div class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">HPP (Cost of Goods Sold)</p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-orange-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($finance['cost'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:package-variant"></span>
                Modal Bahan Baku
            </div>
        </div>

        <!-- Expense Card -->
        <div class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Biaya Operasional</p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-rose-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($finance['expenses'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:cash-remove"></span>
                Listrik, Gaji, dll
            </div>
        </div>

        <!-- Pembayaran Supplier Card -->
        <div class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Pembayaran Supplier</p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-purple-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($finance['supplierPaid'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-purple-600 bg-purple-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:truck-outline"></span>
                Pembelian Stok
            </div>
        </div>
    </div>

    <!-- Baris kedua: Profit & Net Cashflow -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        @if($isSuperAdmin)
<!-- PROFIT Card (Keuntungan Bersih) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-indigo-700 p-7 rounded-[2rem] shadow-xl group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/20 rounded-full opacity-30 group-hover:scale-110 transition-transform"></div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                    <span class="iconify text-white text-xl" data-icon="mdi:cash-multiple"></span>
                </div>
                <p class="text-[11px] font-black text-white/60 uppercase tracking-widest">Keuntungan Bersih</p>
            </div>
            <p class="text-3xl font-black text-white tracking-tight">Rp {{ number_format($finance['profit'], 0, ',', '.') }}</p>
            <p class="text-xs text-white/60 mt-2">Pendapatan - HPP - Biaya Operasional</p>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-indigo-100 bg-white/20 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:chart-line"></span>
                Laba Bersih Minggu Ini
            </div>
        </div>
@else
<!-- Keuntungan Bersih Disembunyikan untuk Cabang -->
@endif

        <!-- Net Cashflow Card -->
        @php $isPositive = $finance['netCashflow'] >= 0; @endphp
        <div class="relative overflow-hidden {{ $isPositive ? 'bg-emerald-600 shadow-emerald-100' : 'bg-rose-600 shadow-rose-100' }} p-7 rounded-[2rem] shadow-xl group">
            <div class="absolute right-0 bottom-0 opacity-10">
                <span class="iconify text-9xl text-white" data-icon="{{ $isPositive ? 'mdi:safe' : 'mdi:alert-circle' }}"></span>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                    <span class="iconify text-white text-xl" data-icon="mdi:currency-usd"></span>
                </div>
                <p class="text-[11px] font-black text-white/60 uppercase tracking-widest">Arus Kas Bersih</p>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-sm font-bold text-white/70">Rp</span>
                <span class="text-3xl font-black text-white tracking-tight">{{ number_format($finance['netCashflow'], 0, ',', '.') }}</span>
            </div>
            <p class="text-white/50 text-[10px] mt-2 font-medium">
                Pendapatan - (Pembayaran Supplier + Operasional)
            </p>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-white/80 bg-white/20 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="{{ $isPositive ? 'mdi:trending-up' : 'mdi:trending-down' }}"></span>
                {{ $isPositive ? 'Surplus Kas' : 'Defisit Kas' }}
            </div>
        </div>
        <!-- Total Kas Tunai -->
        @php
            $netCash = $finance['income_cash'] - $finance['expenses'];
            $isCashPositive = $netCash >= 0;
            $cashBgColor = $isCashPositive ? 'bg-gradient-to-br from-teal-500 to-emerald-600' : 'bg-gradient-to-br from-rose-500 to-red-600';
            $cashShadowColor = $isCashPositive ? 'shadow-emerald-500/30' : 'shadow-rose-500/30';
        @endphp
        <div class="{{$cashBgColor}} rounded-[2rem] p-7 text-white shadow-xl {{$cashShadowColor}} relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors duration-500"></div>
            <div class="absolute right-4 bottom-4 opacity-10">
                <span class="iconify text-8xl" data-icon="mdi:cash-fast"></span>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                    <span class="iconify text-white text-xl" data-icon="mdi:cash-multiple"></span>
                </div>
                <p class="text-[11px] font-black text-white/60 uppercase tracking-widest">Total Kas Tunai</p>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-sm font-bold text-white/70">Rp</span>
                <span class="text-3xl font-black text-white tracking-tight">{{ number_format($netCash, 0, ',', '.') }}</span>
            </div>
            <p class="text-white/50 text-[10px] mt-2 font-medium">
                Pendapatan Tunai - Biaya Operasional
            </p>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-white/80 bg-white/20 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:wallet"></span>
                Uang Fisik Untuk Disetor
            </div>
        </div>
    </div>
    <!-- Chart Section -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase text-sm">Produk Terlaris Minggu Ini</h3>
            <span class="iconify text-slate-300 text-xl" data-icon="mdi:chart-bar"></span>
        </div>
        <div style="height: {{ max(400, $topProducts->count() * 40) }}px; min-height: 400px; position: relative;">
            @if($topProducts->count() > 0)
            <canvas id="weeklyChart"></canvas>
            @else
            <div class="flex flex-col items-center justify-center h-full text-slate-400">
                <span class="iconify text-5xl mb-2" data-icon="mdi:database-off"></span>
                <p class="text-[10px] font-black uppercase tracking-widest">Tidak ada data penjualan</p>
            </div>
            @endif
        </div>
    </div>

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
        const canvas = document.getElementById('weeklyChart');
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
