@extends('layouts.admin')

@section('title', 'Intelligence Hub')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; }
    
    /* Efek Kartu Bertekstur */
    .card-modern { 
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 1);
        border-radius: 28px;
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.04);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-modern:hover { 
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.1);
        border-color: rgba(79, 70, 229, 0.2);
    }

    /* Gradient Background untuk Kontras */
    .bg-gradient-indigo { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
    .bg-gradient-dark { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }

    /* Custom Scrollbar */
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    @keyframes slide-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-main { animation: slide-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
    
    /* Skeleton Loading */
    .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; }
    @keyframes loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>

<div class="p-4 md:p-8 space-y-10 animate-main max-w-[1600px] mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                Pusat <span class="text-indigo-600 italic">Kontrol.</span>
            </h1>
            <p class="text-slate-500 font-semibold flex items-center gap-2">
                <span class="iconify text-indigo-500" data-icon="mdi:view-dashboard-outline"></span>
                Ringkasan performa 
                @if($isSuperAdmin)
                    <span class="text-indigo-600">Semua Cabang</span>
                @else
                    <span class="text-indigo-600">Cabang {{ $branchId }}</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="iconify text-xl" data-icon="mdi:calendar-range"></span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hari Ini</p>
                    <p class="text-sm font-bold text-slate-800">{{ now()->translatedFormat('d M, Y') }}</p>
                </div>
            </div>
            <button onclick="exportReport()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-indigo-200 transition-all font-bold text-sm flex items-center gap-2">
                <span class="iconify" data-icon="mdi:file-pdf-box"></span> Export Laporan
            </button>
        </div>
    </div>

    <!-- Role Badge & Filter Cabang untuk Super Admin -->
    @if($isSuperAdmin)
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-5 rounded-3xl border border-indigo-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center">
                    <span class="iconify text-2xl" data-icon="mdi:crown"></span>
                </div>
                <div>
                    <p class="text-xs font-black text-indigo-600 uppercase tracking-widest">Super Administrator</p>
                    <p class="text-sm font-bold text-slate-700">Akses penuh ke semua {{ $branches->count() }} cabang</p>
                </div>
            </div>
            <div class="flex-1 max-w-md">
                <label class="text-xs font-black text-slate-500 uppercase tracking-widest block mb-2">🏪 Filter Cabang</label>
                <select id="branchFilter" class="w-full p-3 border-2 border-indigo-200 rounded-xl font-semibold bg-white focus:border-indigo-500 focus:outline-none transition-all">
                    <option value="all">📊 Semua Cabang (Agregat)</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        🏪 {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Perbandingan Antar Cabang (Hanya Super Admin) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($branches as $branch)
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all group cursor-pointer" onclick="window.location.href='?branch_id={{ $branch->id }}'">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-black text-indigo-600 uppercase tracking-widest">{{ $branch->name }}</p>
                    <h3 class="text-xl font-black text-slate-800 mt-2">Rp {{ number_format($branchStats[$branch->id]['total_revenue'] ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="iconify text-xl" data-icon="mdi:store"></span>
                </div>
            </div>
            <div class="mt-4 flex justify-between text-xs font-bold">
                <span class="text-slate-500">📦 {{ $branchStats[$branch->id]['total_orders'] ?? 0 }} transaksi</span>
                <span class="text-rose-500 {{ ($branchStats[$branch->id]['low_stock_count'] ?? 0) == 0 ? 'text-emerald-500' : '' }}">
                    ⚠️ {{ $branchStats[$branch->id]['low_stock_count'] ?? 0 }} stok menipis
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Info Cabang untuk Non-Super Admin -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5 rounded-3xl text-white shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                <span class="iconify text-3xl" data-icon="mdi:store-check"></span>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest opacity-80">Cabang Aktif</p>
                <p class="text-xl font-bold">ID: {{ $branchId }} | Role: {{ ucfirst(auth()->user()->role) }}</p>
                <p class="text-sm opacity-90 mt-1">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $metrics = [
                ['label' => 'Total Menu', 'val' => $totalProducts, 'icon' => 'mdi:food', 'color' => 'indigo', 'trend' => '+12%', 'bg' => 'indigo-50', 'text' => 'indigo-600'],
                ['label' => 'Stok Bahan Menipis', 'val' => $lowStock, 'icon' => 'mdi:alert-decagram', 'color' => 'amber', 'trend' => $lowStock > 0 ? 'Perlu restock' : 'Aman', 'bg' => 'amber-50', 'text' => 'amber-600'],
                ['label' => 'Stok Bahan Habis', 'val' => $outOfStock, 'icon' => 'mdi:close-octagon', 'color' => 'rose', 'trend' => $outOfStock > 0 ? 'Kritis!' : 'Habis', 'bg' => 'rose-50', 'text' => 'rose-600'],
                ['label' => 'User Aktif', 'val' => $totalUsers, 'icon' => 'mdi:account-group', 'color' => 'emerald', 'trend' => 'Terdaftar', 'bg' => 'emerald-50', 'text' => 'emerald-600'],
            ];
        @endphp
        @foreach($metrics as $m)
        <div class="card-modern p-6 relative overflow-hidden group">
            <div class="flex justify-between items-start relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-{{ $m['bg'] }} text-{{ $m['text'] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="iconify text-2xl" data-icon="{{ $m['icon'] }}"></span>
                </div>
                <span class="text-[10px] font-black px-2 py-1 bg-{{ $m['bg'] }} text-{{ $m['text'] }} rounded-lg">{{ $m['trend'] }}</span>
            </div>
            <div class="mt-5 relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $m['label'] }}</p>
                <h2 class="text-3xl font-black text-slate-800 mt-1 tracking-tighter">{{ number_format($m['val']) }}</h2>
            </div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-{{ $m['color'] }}-500/5 rounded-full blur-xl"></div>
        </div>
        @endforeach
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Charts & Financial -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Revenue Chart -->
            <div class="card-modern p-8 bg-white/40">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Analisis Arus Kas</h3>
                        <p class="text-sm font-medium text-slate-400 mt-1">Perbandingan Pemasukan & Pengeluaran Bulanan {{ now()->year }}</p>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl shadow-sm">
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                            <span class="text-[10px] font-black text-slate-600 uppercase">Pemasukan</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl">
                            <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                            <span class="text-[10px] font-black text-slate-600 uppercase">Pengeluaran</span>
                        </div>
                    </div>
                </div>
                <div id="mainRevenueChart" class="w-full min-h-[400px]"></div>
            </div>

            <!-- Financial Cards: Piutang & Hutang -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-indigo p-8 rounded-[2.5rem] text-white shadow-xl shadow-indigo-200 relative overflow-hidden group cursor-pointer" onclick="showDetail('piutang')">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black uppercase tracking-widest opacity-70">Dana Piutang</p>
                        <h4 class="text-3xl font-black mt-2 tracking-tighter">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h4>
                        <div class="mt-6 flex items-center gap-2 bg-white/10 w-fit px-4 py-2 rounded-full backdrop-blur-md border border-white/10">
                            <span class="iconify text-emerald-300" data-icon="mdi:trending-up"></span>
                            <span class="text-[11px] font-bold">{{ $totalPiutang > 0 ? 'Dana masuk tertunda' : 'Tidak ada piutang' }}</span>
                        </div>
                    </div>
                    <span class="iconify absolute -bottom-10 -right-10 text-[180px] opacity-10 rotate-12 group-hover:rotate-0 transition-transform duration-700" data-icon="mdi:bank-transfer-in"></span>
                </div>

                <div class="bg-gradient-dark p-8 rounded-[2.5rem] text-white shadow-xl shadow-slate-200 relative overflow-hidden group cursor-pointer" onclick="showDetail('hutang')">
                    <div class="relative z-10 text-right">
                        <p class="text-[11px] font-black uppercase tracking-widest opacity-70">Dana Hutang</p>
                        <h4 class="text-3xl font-black mt-2 tracking-tighter">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h4>
                        <div class="mt-6 flex items-center gap-2 bg-white/10 w-fit ml-auto px-4 py-2 rounded-full backdrop-blur-md border border-white/10">
                            <span class="text-[11px] font-bold text-rose-300">{{ $totalHutang > 0 ? 'Kewajiban pembayaran' : 'Tidak ada hutang' }}</span>
                            <span class="iconify text-rose-300" data-icon="mdi:alert-circle-outline"></span>
                        </div>
                    </div>
                    <span class="iconify absolute -bottom-10 -left-10 text-[180px] opacity-10 -rotate-12 group-hover:rotate-0 transition-transform duration-700" data-icon="mdi:bank-transfer-out"></span>
                </div>
            </div>
        </div>

        <!-- Right Column: Stock Distribution & Activity -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- Stock Donut Chart -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col relative overflow-hidden">
                <div class="mb-6">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter italic">Nadi Stok</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Konsentrasi Kategori Produk</p>
                </div>
                <div id="mainStockChart" class="w-full flex-1 min-h-[350px]"></div>
                <div class="mt-6 flex justify-between border-t border-slate-50 pt-6">
                    <div class="text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Optimasi</p>
                        <p class="text-lg font-black text-slate-800">{{ count($stockNames) > 0 ? '94%' : '0%' }}</p>
                    </div>
                    <div class="h-8 w-[1px] bg-slate-100"></div>
                    <div class="text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Diversitas</p>
                        <p class="text-lg font-black text-slate-800">{{ count($stockNames) }} Kategori</p>
                    </div>
                    <div class="h-8 w-[1px] bg-slate-100"></div>
                    <div class="text-center">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Unit</p>
                        <p class="text-lg font-black text-slate-800">{{ array_sum($stockSeries) }}</p>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card-modern overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">📋 Aktivitas Terakhir</h4>
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                </div>
                <div class="p-2 custom-scroll max-h-[320px] overflow-y-auto" id="activityLog">
                    <!-- Loading Skeleton -->
                    <div class="space-y-2">
                        <div class="skeleton h-16 rounded-xl"></div>
                        <div class="skeleton h-16 rounded-xl"></div>
                        <div class="skeleton h-16 rounded-xl"></div>
                    </div>
                </div>
                <button onclick="loadAllActivities()" class="w-full py-4 bg-slate-50 text-[10px] font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-[0.3em]">
                    Lihat Semua Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let revenueChart, stockChart;
    
    // Dashboard Data dari Server
    const dashboardData = {
        months: @json($months ?? []),
        revenues: @json($revenues ?? []),
        expenses: @json($expenses ?? []),
        stockNames: @json($stockNames ?? []),
        stockSeries: @json($stockSeries ?? []).map(v => parseInt(v) || 0),
        isSuperAdmin: @json($isSuperAdmin),
        currentBranchId: @json($branchId),
        totalPiutang: @json($totalPiutang),
        totalHutang: @json($totalHutang)
    };

    // Render Charts
    function renderCharts() {
        // Revenue Chart
        const revenueEl = document.querySelector("#mainRevenueChart");
        if (revenueEl) {
            if (revenueChart) revenueChart.destroy();
            revenueEl.innerHTML = '';
            
            const revenueOptions = {
                chart: { type: 'area', height: 400, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                series: [
                    { name: 'Pemasukan', data: dashboardData.revenues },
                    { name: 'Pengeluaran', data: dashboardData.expenses }
                ],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] } },
                colors: ['#4f46e5', '#f43f5e'],
                stroke: { curve: 'smooth', width: 4, lineCap: 'round' },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                xaxis: { 
                    categories: dashboardData.months,
                    axisBorder: { show: false }, axisTicks: { show: false },
                    labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 } }
                },
                yaxis: { 
                    labels: { 
                        style: { colors: '#94a3b8', fontWeight: 600, fontSize: '11px' },
                        formatter: val => val >= 1000000 ? (val/1000000).toFixed(1) + 'M' : (val/1000).toFixed(0) + 'K'
                    } 
                },
                tooltip: { theme: 'dark', y: { formatter: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) } },
                legend: { show: false }
            };
            revenueChart = new ApexCharts(revenueEl, revenueOptions);
            revenueChart.render();
        }

        // Stock Chart
        const stockEl = document.querySelector("#mainStockChart");
        if (stockEl && dashboardData.stockSeries.length > 0) {
            if (stockChart) stockChart.destroy();
            stockEl.innerHTML = '';
            
            const stockOptions = {
                chart: { type: 'donut', height: 380, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                series: dashboardData.stockSeries,
                labels: dashboardData.stockNames,
                colors: ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#a855f7', '#0ea5e9', '#14b8a6', '#8b5cf6'],
                legend: { 
                    position: 'bottom', fontSize: '11px', fontWeight: 700, 
                    labels: { colors: '#64748b' }, markers: { radius: 6, width: 8, height: 8 } 
                },
                stroke: { width: 4, colors: ['#ffffff'] },
                plotOptions: {
                    pie: { 
                        donut: { 
                            size: '75%',
                            labels: {
                                show: true,
                                total: { 
                                    show: true, label: 'Total Stok', fontSize: '10px', fontWeight: '800', color: '#94a3b8',
                                    formatter: () => dashboardData.stockSeries.reduce((a,b) => a+b, 0)
                                },
                                value: { fontSize: '28px', fontWeight: '800', color: '#1e293b', offsetY: 5 }
                            }
                        } 
                    }
                },
                dataLabels: { enabled: false }
            };
            stockChart = new ApexCharts(stockEl, stockOptions);
            stockChart.render();
        } else if (stockEl) {
            stockEl.innerHTML = '<div class="text-center py-20 text-slate-400">📦 Belum ada data stok</div>';
        }
    }

    // Load Activity Log via AJAX
    // Load Activity Log via AJAX
function loadActivities(branchId = null) {
    let url = '/dashboard/activities';
    if (branchId && branchId !== 'all') {
        url += '?branch_id=' + branchId;
    }
    
    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            const container = $('#activityLog');
            if (response.activities && response.activities.length > 0) {
                let html = '';
                response.activities.forEach(activity => {
                    const icon = activity.icon || getActivityIcon(activity.type);
                    const color = activity.color || getActivityColor(activity.type);
                    html += `
                        <div class="flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors rounded-2xl cursor-pointer group">
                            <div class="w-10 h-10 rounded-xl bg-${color}-50 text-${color}-600 flex items-center justify-center shrink-0">
                                <span class="iconify text-xl" data-icon="${icon}"></span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">${activity.message}</p>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">${activity.time}</p>
                            </div>
                        </div>
                    `;
                });
                container.html(html);
            } else {
                container.html('<div class="text-center py-10 text-slate-400">✨ Belum ada aktivitas transaksi</div>');
            }
            if (window.Iconify) window.Iconify.scan();
        },
        error: function(xhr) {
            console.error('Error loading activities:', xhr);
            $('#activityLog').html('<div class="text-center py-10 text-rose-500">⚠️ Gagal memuat aktivitas</div>');
        }
    });
}

    function getActivityIcon(type) {
        const icons = { order: 'mdi:shopping', stock: 'mdi:package-down', payment: 'mdi:cash', user: 'mdi:account' };
        return icons[type] || 'mdi:bell';
    }

    function getActivityColor(type) {
        const colors = { order: 'indigo', stock: 'emerald', payment: 'amber', user: 'purple' };
        return colors[type] || 'slate';
    }

    function loadAllActivities() {
        loadActivities($('#branchFilter').val());
    }

    // Filter Branch (untuk Super Admin)
    $('#branchFilter').on('change', function() {
        const branchId = $(this).val();
        window.location.href = '?branch_id=' + branchId;
    });

    // Export Report
    function exportReport() {
        const branchId = $('#branchFilter').val();
        window.open('/dashboard/export?branch_id=' + (branchId || 'all'), '_blank');
    }

    // Show Detail Modal
    function showDetail(type) {
        const branchId = $('#branchFilter').val();
        const amount = type === 'piutang' ? dashboardData.totalPiutang : dashboardData.totalHutang;
        alert(`${type.toUpperCase()}: Rp ${new Intl.NumberFormat('id-ID').format(amount)}\nBranch: ${branchId === 'all' ? 'Semua Cabang' : branchId}`);
    }

    // Initial Load
    $(document).ready(function() {
        renderCharts();
        loadActivities($('#branchFilter').val());
    });

    // Turbo Drive / Livewire Support
    document.addEventListener("turbo:load", function() {
        if (window.Iconify) window.Iconify.scan();
        setTimeout(() => { renderCharts(); loadActivities($('#branchFilter').val()); }, 100);
    });
</script>
@endsection