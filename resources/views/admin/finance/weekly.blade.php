@extends('layouts.admin')

@section('title', 'Laporan Mingguan')

@section('content')
<div class="space-y-8">

    <!-- Branding & Range -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:calendar-week"></span>
                </span>
                Laporan Mingguan
            </h1>
            <p class="text-slate-500 font-medium">{{ $start->translatedFormat('d M Y') }} - {{ $end->translatedFormat('d
                M Y') }}</p>
        </div>

        <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl">
            <a href="{{ route('admin.finance.index') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.index') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Ringkasan</a>
            <a href="{{ route('admin.finance.daily') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.daily') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Harian</a>
            <a href="{{ route('admin.finance.weekly') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.weekly') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Mingguan</a>
            <a href="{{ route('admin.finance.monthly') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.finance.monthly') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Bulanan</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Income Card -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Total Pendapatan
            </p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-emerald-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($finance['income'], 0,
                    ',', '.') }}</span>
            </div>
            <div
                class="mt-4 flex items-center gap-2 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:trending-up"></span>
                Minggu Ini
            </div>
        </div>

        <!-- Expense Card -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Pengeluaran
                Operasional</p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-rose-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($finance['expenses'],
                    0, ',', '.') }}</span>
            </div>
            <div
                class="mt-4 flex items-center gap-2 text-[10px] font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:cash-remove"></span>
                Biaya Operasional
            </div>
        </div>

        <!-- Supplier Paid Card -->
        <div
            class="relative overflow-hidden bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 relative">Bayar Supplier</p>
            <div class="flex items-baseline gap-1 relative">
                <span class="text-sm font-bold text-orange-500">Rp</span>
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{
                    number_format($finance['supplierPaid'], 0, ',', '.') }}</span>
            </div>
            <div
                class="mt-4 flex items-center gap-2 text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-full w-fit">
                <span class="iconify" data-icon="mdi:truck-outline"></span>
                Pembelian Stok
            </div>
        </div>

        <!-- Net Balance -->
        @php $isPositive = $finance['netCashflow'] >= 0; @endphp
        <div
            class="relative overflow-hidden {{ $isPositive ? 'bg-indigo-600 shadow-indigo-100' : 'bg-rose-600 shadow-rose-100' }} p-7 rounded-[2rem] shadow-xl md:col-span-3 block">
            <div class="absolute right-0 bottom-0 opacity-10">
                <span class="iconify text-9xl text-white"
                    data-icon="{{ $isPositive ? 'mdi:piggy-bank' : 'mdi:alert-circle' }}"></span>
            </div>
            <p class="text-[11px] font-black text-white/60 uppercase tracking-[0.2em] mb-3">Saldo Bersih (Net Cashflow)
            </p>
            <div class="flex items-baseline gap-1">
                <span class="text-sm font-bold text-white/70">Rp</span>
                <span class="text-4xl font-black text-white tracking-tighter">{{ number_format($finance['netCashflow'],
                    0, ',', '.') }}</span>
            </div>
            <p class="text-white/50 text-[10px] mt-4 font-medium uppercase tracking-widest">
                {{ $isPositive ? 'Surplus Selisih Kas' : 'Defisit Selisih Kas' }}
            </p>
        </div>

    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase text-sm">Produk Terlaris Minggu Ini
            </h3>
            <span class="iconify text-slate-300 text-xl" data-icon="mdi:chart-bar"></span>
        </div>
        <div class="h-[400px]">
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
@if($topProducts->count() > 0)
<script>
    document.addEventListener('turbo:load', function () {
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
});
</script>
@endif
@endpush
@endsection
