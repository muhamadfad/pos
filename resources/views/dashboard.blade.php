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
    .bg-gradient-success { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }

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
                <span class="text-indigo-600">{{ $currentBranchName }}</span>
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

    <!-- Filter Cabang untuk Super Admin -->
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
                <label class="text-xs font-black text-slate-500 uppercase tracking-widest block mb-2 flex items-center gap-1"><span class="iconify" data-icon="mdi:store-outline"></span> Filter Cabang</label>
                <select id="branchFilter" class="w-full p-3 border-2 border-indigo-200 rounded-xl font-semibold bg-white focus:border-indigo-500 focus:outline-none transition-all">
                    <option value="all" {{ $showAllBranches ? 'selected' : '' }}>Semua Cabang (Gabungan)</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (!$showAllBranches && $branchId == $branch->id) ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif

    <!-- Perbandingan Antar Cabang (Hanya Super Admin ketika melihat "Semua Cabang") -->
    @if($isSuperAdmin && $showAllBranches)
    <div>
        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2"><span class="iconify text-lg" data-icon="mdi:chart-bar"></span> Perbandingan Performa Cabang</h3>
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
                    <span class="text-slate-500 flex items-center gap-1"><span class="iconify" data-icon="mdi:package-variant"></span> {{ $branchStats[$branch->id]['total_orders'] ?? 0 }} transaksi</span>
                    <span class="{{ ($branchStats[$branch->id]['low_stock_count'] ?? 0) > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                        <span class="iconify" data-icon="mdi:alert-outline"></span> {{ $branchStats[$branch->id]['low_stock_count'] ?? 0 }} stok menipis
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif(!$isSuperAdmin)
    <!-- Info Cabang untuk Non-Super Admin -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5 rounded-3xl text-white shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                <span class="iconify text-3xl" data-icon="mdi:store-check"></span>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest opacity-80">Cabang Aktif</p>
                <p class="text-xl font-bold">{{ $currentBranchName }}</p>
                <p class="text-sm opacity-90 mt-1">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- KPI Cards (5 cards termasuk Pendapatan Hari Ini) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        
        <!-- Card Pendapatan Hari Ini -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-3xl text-white shadow-xl shadow-emerald-200 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="iconify text-2xl" data-icon="mdi:cash-multiple"></span>
                    </div>
                    <span class="text-[10px] font-black px-2 py-1 bg-white/20 backdrop-blur rounded-lg {{ $revenueChange >= 0 ? 'bg-emerald-400/30' : 'bg-rose-400/30' }}">
                        <span class="iconify text-xs" data-icon="{{ $revenueChange >= 0 ? 'mdi:trending-up' : 'mdi:trending-down' }}"></span>
                        {{ $revenueChange >= 0 ? '+' : '' }}{{ number_format($revenueChange, 1) }}% dari kemarin
                    </span>
                </div>
                <div class="mt-5">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Pendapatan Hari Ini</p>
                    <h2 class="text-3xl font-black mt-1 tracking-tighter">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h2>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="iconify text-sm" data-icon="mdi:receipt"></span>
                        <span class="text-xs font-semibold">{{ $todayTransactions }} transaksi</span>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -top-4 -left-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        </div>

        @php
            $metrics = [
                ['label' => 'Total Menu', 'val' => $totalProducts, 'icon' => 'mdi:food', 'color' => 'indigo', 'trend' => 'Aktif', 'bg' => 'indigo-50', 'text' => 'indigo-600'],
                ['label' => 'Stok Bahan Menipis', 'val' => $lowStock, 'icon' => 'mdi:alert-decagram', 'color' => 'amber', 'trend' => $lowStock > 0 ? 'Perlu restock' : 'Aman', 'bg' => 'amber-50', 'text' => 'amber-600'],
                ['label' => 'Stok Bahan Habis', 'val' => $outOfStock, 'icon' => 'mdi:close-octagon', 'color' => 'rose', 'trend' => $outOfStock > 0 ? 'Kritis!' : 'Aman', 'bg' => 'rose-50', 'text' => 'rose-600'],
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 w-full max-w-full">
        
        <!-- Left Column: Charts & Financial (mengambil 8 kolom penuh) -->
        <div class="lg:col-span-12 space-y-8 min-w-0 w-full">
            
            <!-- Revenue Chart -->
            <div class="card-modern p-6 sm:p-8 bg-white/40 overflow-hidden min-w-0 w-full">
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

            <!-- Financial Cards: Piutang & Hutang (2 kolom) -->
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
            
            <!-- Activity Log (Full Width) -->
            <div class="card-modern overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2"><span class="iconify text-lg" data-icon="mdi:clipboard-text-outline"></span> Aktivitas Terakhir</h4>
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
            </div>
        </div>
    </div>
</div>
<script>
    var revenueChart;
    
    // Dashboard Data dari Server
    var dashboardData = {
        months: @json($months ?? []),
        revenues: @json($revenues ?? []),
        expenses: @json($expenses ?? []),
        isSuperAdmin: @json($isSuperAdmin),
        currentBranchId: @json($branchId),
        showAllBranches: @json($showAllBranches),
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
                stroke: { curve: 'monotoneCubic', width: 4, lineCap: 'round' },
                grid: { 
                    borderColor: '#f1f5f9', 
                    strokeDashArray: 4,
                    padding: { top: 30, right: 10, left: 10, bottom: 0 }
                },
                xaxis: { 
                    categories: dashboardData.months,
                    axisBorder: { show: false }, axisTicks: { show: false },
                    labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 600 } }
                },
                yaxis: { 
                    min: 0,
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
    }

    // Load Activity Log via AJAX
    function loadActivities() {
        const branchId = $('#branchFilter').val();
        let url = '/dashboard/activities';
        if (branchId && branchId !== 'all') {
            url += '?branch_id=' + branchId;
        }
        
        $('#activityLog').html(`
            <div class="space-y-2">
                <div class="skeleton h-16 rounded-xl"></div>
                <div class="skeleton h-16 rounded-xl"></div>
                <div class="skeleton h-16 rounded-xl"></div>
            </div>
        `);
        
        $.ajax({
            url: url,
            method: 'GET',
            timeout: 30000, // 30 seconds timeout
            success: function(response) {
                const container = $('#activityLog');
                if (response.success === false) {
                    container.html(`
                        <div class="text-center py-10 text-rose-500">
                            <span class="iconify text-3xl mb-2" data-icon="mdi:alert-circle"></span>
                            <p class="text-sm">${response.message || 'Gagal memuat aktivitas'}</p>
                            <button onclick="loadActivities()" class="mt-3 text-indigo-600 text-xs font-semibold underline">Coba lagi</button>
                        </div>
                    `);
                    return;
                }
                
                if (response.activities && response.activities.length > 0) {
                    let html = '';
                    response.activities.forEach(activity => {
                        const icon = activity.icon || getActivityIcon(activity.type);
                        const color = activity.color || getActivityColor(activity.type);
                        html += `
                            <div class="flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors rounded-2xl">
                                <div class="w-10 h-10 rounded-xl bg-${color}-50 text-${color}-600 flex items-center justify-center shrink-0">
                                    <span class="iconify text-xl" data-icon="${icon}"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-700">${escapeHtml(activity.message)}</p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">${activity.time}</p>
                                </div>
                            </div>
                        `;
                    });
                    container.html(html);
                } else {
                    container.html(`
                        <div class="text-center py-10 text-slate-400">
                            <span class="iconify text-3xl mb-2" data-icon="mdi:inbox-outline"></span>
                            <p class="text-sm">Belum ada aktivitas transaksi</p>
                        </div>
                    `);
                }
                if (window.Iconify) window.Iconify.scan();
            },
            error: function(xhr, status, error) {
                console.error('Error loading activities:', error);
                let errorMessage = 'Gagal memuat aktivitas';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    errorMessage = 'Waktu habis, silakan coba lagi';
                }
                $('#activityLog').html(`
                    <div class="text-center py-10 text-rose-500">
                        <span class="iconify text-3xl mb-2" data-icon="mdi:alert-circle"></span>
                        <p class="text-sm">${errorMessage}</p>
                        <button onclick="loadActivities()" class="mt-3 text-indigo-600 text-xs font-semibold underline">Coba lagi</button>
                    </div>
                `);
            }
        });
    }

    // Helper function untuk escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    function getActivityIcon(type) {
        const icons = { order: 'mdi:cart', stock: 'mdi:alert', payment: 'mdi:cash-clock' };
        return icons[type] || 'mdi:bell';
    }

    function getActivityColor(type) {
        const colors = { order: 'indigo', stock: 'amber', payment: 'purple' };
        return colors[type] || 'slate';
    }

    // Filter Branch (untuk Super Admin)
    $(document).ready(function() {
        $('#branchFilter').on('change', function() {
            const branchId = $(this).val();
            window.location.href = '?branch_id=' + branchId;
        });
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
        const branchText = dashboardData.isSuperAdmin ? (branchId === 'all' ? 'Semua Cabang' : 'Cabang ' + branchId) : dashboardData.currentBranchId;
        alert(`${type.toUpperCase()}: Rp ${new Intl.NumberFormat('id-ID').format(amount)}\n${branchText}`);
    }

    // Execute using polling that checks if DOM is painted
    window.initDashboard = function() {
        if (typeof ApexCharts !== 'undefined' && typeof $ !== 'undefined') {
            const revenueEl = document.querySelector("#mainRevenueChart");
            
            // Wait until the browser has fully painted the DOM and container has width
            if (revenueEl && revenueEl.offsetWidth === 0) {
                setTimeout(window.initDashboard, 50);
                return;
            }

            if (window.Iconify) window.Iconify.scan();
            renderCharts(); 
            loadActivities(); 
        } else {
            setTimeout(window.initDashboard, 50);
        }
    };

    window.initDashboard();
</script>
@endsection