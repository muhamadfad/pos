@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl font-semibold text-black tracking-tight">Overview</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">
                Performa bisnis <span class="text-black font-bold">{{ $currentBranchName }}</span> hari ini
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-md border border-gray-200 shadow-sm">
                <span class="iconify text-gray-400" data-icon="lucide:calendar-days"></span>
                <span class="text-sm font-bold text-slate-700">{{ now()->translatedFormat('d M Y') }}</span>
            </div>
            <button onclick="exportReport()" class="flex items-center gap-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-md shadow-sm transition-all font-medium text-sm">
                <span class="iconify" data-icon="lucide:download"></span>
                Export Data
            </button>
        </div>
    </div>

    <!-- Filter (Super Admin) -->
    @if($isSuperAdmin)
    <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-md bg-gray-50 border border-gray-200 flex items-center justify-center">
                <span class="iconify text-gray-600" data-icon="lucide:layout-grid"></span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Filter Cabang</p>
                <select id="branchFilter" class="w-64 border-none text-sm font-bold text-slate-800 bg-transparent focus:ring-0 p-0 cursor-pointer outline-none">
                    <option value="all" {{ $showAllBranches ? 'selected' : '' }}>Semua Cabang (Gabungan)</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ (!$showAllBranches && $branchId == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistic Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Pendapatan -->
        <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm hover:shadow-sm transition-all">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Pendapatan</p>
                    <h3 class="text-xl font-semibold text-black tracking-tight">Rp{{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center">
                    <span class="iconify text-xl text-black" data-icon="lucide:wallet"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-xs font-bold {{ $revenueChange >= 0 ? 'text-black' : 'text-rose-500' }}">
                    <span class="iconify" data-icon="{{ $revenueChange >= 0 ? 'lucide:trending-up' : 'lucide:trending-down' }}"></span>
                    {{ $revenueChange >= 0 ? '+' : '' }}{{ number_format($revenueChange, 1) }}%
                </div>
                <span class="text-xs font-semibold text-gray-400">{{ number_format($todayTransactions) }} transaksi</span>
            </div>
        </div>

        <!-- Total Menu -->
        <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm hover:shadow-sm transition-all">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Menu Aktif</p>
                    <h3 class="text-xl font-semibold text-black tracking-tight">{{ number_format($totalProducts) }}</h3>
                </div>
                <div class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center">
                    <span class="iconify text-xl text-black" data-icon="lucide:coffee"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400">Status Katalog</span>
                <div class="flex items-center gap-1 text-xs font-bold text-black">
                    <span class="iconify" data-icon="lucide:check-circle-2"></span>
                    Normal
                </div>
            </div>
        </div>

        <!-- Stok -->
        <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm hover:shadow-sm transition-all">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Stok Menipis</p>
                    <h3 class="text-xl font-semibold text-black tracking-tight">{{ number_format($lowStock) }}</h3>
                </div>
                <div class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center">
                    <span class="iconify text-xl text-black" data-icon="lucide:alert-circle"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400">Stok Habis</span>
                <div class="flex items-center gap-1 text-xs font-bold {{ $outOfStock > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                    {{ number_format($outOfStock) }} item
                </div>
            </div>
        </div>

        <!-- Finansial -->
        <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm hover:shadow-sm transition-all">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Dana Tertunda</p>
                    <h3 class="text-xl font-semibold text-black tracking-tight">Rp{{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Piutang</p>
                </div>
                <div class="w-8 h-8 rounded-md bg-white border border-gray-200 flex items-center justify-center">
                    <span class="iconify text-xl text-black" data-icon="lucide:receipt"></span>
                </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <span class="text-xs font-semibold text-gray-400">Hutang Suplier</span>
                <span class="text-xs font-bold text-rose-500">Rp{{ number_format($totalHutang, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Charts & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-2">
        
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 shadow-sm p-4 md:p-5 min-w-0 overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-base font-semibold text-black">Arus Kas {{ now()->year }}</h3>
                    <p class="text-sm font-medium text-gray-500">Statistik pendapatan vs pengeluaran</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-md bg-black"></div><span class="text-xs font-bold text-gray-600">Pemasukan</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-md bg-gray-400"></div><span class="text-xs font-bold text-gray-600">Pengeluaran</span></div>
                </div>
            </div>
            <div id="mainRevenueChart" class="w-full min-h-[350px] max-w-full overflow-hidden"></div>
        </div>

        <!-- Activity -->
        <div class="bg-white rounded-md border border-gray-200 shadow-sm flex flex-col overflow-hidden">
            <div class="px-6 py-6 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-base font-semibold text-black">Aktivitas Terkini</h3>
                <button onclick="loadActivities()" class="text-gray-400 hover:text-black transition-colors bg-white w-8 h-8 rounded-md flex items-center justify-center shadow-sm border border-gray-200">
                    <span class="iconify" data-icon="lucide:rotate-cw"></span>
                </button>
            </div>
            <div class="p-2 flex-1 overflow-y-auto max-h-[400px] custom-scroll" id="activityLog">
                <!-- Loaded via JS -->
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
                dataLabels: { enabled: false },
                chart: { type: 'area', height: 400, width: '100%', toolbar: { show: false }, fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif' },
                series: [
                    { name: 'Pemasukan', data: dashboardData.revenues },
                    { name: 'Pengeluaran', data: dashboardData.expenses }
                ],
                fill: { type: 'solid' },
                colors: ['#000000', '#9ca3af'],
                stroke: { curve: 'monotoneCubic', width: 2, lineCap: 'round' },
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
                <div class="skeleton h-16 rounded-md"></div>
                <div class="skeleton h-16 rounded-md"></div>
                <div class="skeleton h-16 rounded-md"></div>
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
                            <span class="iconify text-xl mb-2" data-icon="lucide:alert-triangle-circle"></span>
                            <p class="text-sm">${response.message || 'Gagal memuat aktivitas'}</p>
                            <button onclick="loadActivities()" class="mt-3 text-black text-xs font-semibold underline">Coba lagi</button>
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
                            <div class="flex items-center gap-4 py-3 px-4 hover:bg-gray-50 text-sm transition-colors rounded-md">
                                <div class="w-8 h-8 rounded-md bg-${color}-50 text-${color}-600 flex items-center justify-center shrink-0">
                                    <span class="iconify text-xl" data-icon="${icon}"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-700">${escapeHtml(activity.message)}</p>
                                    <p class="text-[10px] font-semibold text-gray-500  mt-0.5">${activity.time}</p>
                                </div>
                            </div>
                        `;
                    });
                    container.html(html);
                } else {
                    container.html(`
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-md flex items-center justify-center mb-3"><span class="iconify text-2xl" data-icon="lucide:inbox"></span></div>
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
                        <span class="iconify text-xl mb-2" data-icon="lucide:alert-triangle-circle"></span>
                        <p class="text-sm">${errorMessage}</p>
                        <button onclick="loadActivities()" class="mt-3 text-black text-xs font-semibold underline">Coba lagi</button>
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
        const icons = { order: 'lucide:shopping-cart', stock: 'lucide:alert-triangle', payment: 'lucide:credit-card' };
        return icons[type] || 'lucide:bell';
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
