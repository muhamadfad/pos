@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-2">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">
                Selamat Datang, {{ Auth::user()->name ?? 'Admin' }} 👋
            </h1>
            <p class="text-slate-500 mt-1">
                Ringkasan performa dan aktivitas sistem hari ini
            </p>
        </div>

        <div class="text-sm text-slate-500">
            {{ now()->format('d F Y') }}
        </div>
    </div>


    <!-- KPI + FINANCE COMPACT -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-2">

    <!-- Total Barang -->
    <div class="group bg-white p-5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Total Barang</p>
                <h2 class="text-xl font-semibold mt-1">{{ $totalProducts }}</h2>
            </div>
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 group-hover:bg-blue-100 transition">
                <span class="iconify text-xl text-blue-600" data-icon="mdi:package-variant-closed"></span>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="group bg-white p-5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Stok Menipis</p>
                <h2 class="text-xl font-semibold mt-1 text-amber-500">{{ $lowStock }}</h2>
            </div>
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 group-hover:bg-amber-100 transition">
                <span class="iconify text-xl text-amber-600" data-icon="mdi:alert-circle-outline"></span>
            </div>
        </div>
    </div>

    <!-- Stok Habis -->
    <div class="group bg-white p-5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Stok Habis</p>
                <h2 class="text-xl font-semibold mt-1 text-red-500">{{ $outOfStock ?? 0 }}</h2>
            </div>
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 group-hover:bg-red-100 transition">
                <span class="iconify text-xl text-red-600" data-icon="mdi:close-circle-outline"></span>
            </div>
        </div>
    </div>

    <!-- Total User -->
    <div class="group bg-white p-5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Total User</p>
                <h2 class="text-xl font-semibold mt-1">{{ $totalUsers }}</h2>
            </div>
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-green-50 group-hover:bg-green-100 transition">
                <span class="iconify text-xl text-green-600" data-icon="mdi:account-group-outline"></span>
            </div>
        </div>
    </div>

    <!-- Total Piutang -->
    <div class="group bg-white p-5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Total Piutang</p>
                <h2 class="text-xl font-semibold mt-1 text-emerald-600">Rp 12.5jt</h2>
            </div>
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-50 group-hover:bg-emerald-100 transition">
                <span class="iconify text-xl text-emerald-600" data-icon="mdi:cash-plus"></span>
            </div>
        </div>
    </div>

    <!-- Total Hutang -->
    <div class="group bg-white p-5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Total Hutang</p>
                <h2 class="text-xl font-semibold mt-1 text-rose-600">Rp 8.7jt</h2>
            </div>
            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-50 group-hover:bg-rose-100 transition">
                <span class="iconify text-xl text-rose-600" data-icon="mdi:cash-minus"></span>
            </div>
        </div>
    </div>

</div>

        <!-- CHART SECTION -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-2 items-stretch">

    <!-- AREA CHART -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-slate-700">
                Ringkasan Keuangan
            </h3>
            <p class="text-sm text-slate-400 mt-1">
                Pendapatan & Pengeluaran
            </p>
        </div>

        <div class="p-4 flex-1">
            <div id="revenueChart" class="w-full h-[320px]"></div>
        </div>
    </div>


    <!-- DONUT CHART -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-slate-700">
                Distribusi Stok
            </h3>
            <p class="text-sm text-slate-400 mt-1">
                Komposisi kategori
            </p>
        </div>

        <div class="p-6 flex-1">
            <div id="stockChart" class="w-full h-[320px]"></div>
        </div>
    </div>

</div>





    <!-- ACTIVITY LOG -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-6 border-b font-semibold">Activity Log</div>
        <div class="p-6 space-y-4 text-sm text-slate-600">

            <div class="flex justify-between">
                <span>Admin menambahkan produk baru</span>
                <span class="text-slate-400">5 menit lalu</span>
            </div>

            <div class="flex justify-between">
                <span>Transaksi barang keluar</span>
                <span class="text-slate-400">30 menit lalu</span>
            </div>

            <div class="flex justify-between">
                <span>User baru terdaftar</span>
                <span class="text-slate-400">1 jam lalu</span>
            </div>

        </div>
    </div>

</div>



<script>
document.addEventListener("DOMContentLoaded", function () {

    let revenueChart = null;
    let stockChart = null;

    function initCharts() {

        const revenueEl = document.querySelector("#revenueChart");
        const stockEl = document.querySelector("#stockChart");

        if (!revenueEl || !stockEl) return;

        if (revenueChart) revenueChart.destroy();
        if (stockChart) stockChart.destroy();

        revenueChart = new ApexCharts(revenueEl, {
            chart: {
                type: 'area',
                height: '100%',
                width: '100%',
                toolbar: { show: false },
                redrawOnParentResize: true,
                redrawOnWindowResize: true
            },
            series: [
                { name: 'Pendapatan', data: [82,90,95,102,110,118] },
                { name: 'Pengeluaran', data: [65,70,73,76,80,84] }
            ],
            xaxis: {
                categories: ['Jan','Feb','Mar','Apr','Mei','Jun']
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#6366f1','#ef4444'],
            dataLabels: { enabled: false }
        });

        revenueChart.render();

        stockChart = new ApexCharts(stockEl, {
            chart: {
                type: 'donut',
                height: '100%',
                width: '100%',
                redrawOnParentResize: true,
                redrawOnWindowResize: true
            },
            series: [420,310,190,150],
            labels: ['Semen','Besi','Keramik','Cat'],
            legend: { position: 'bottom' }
        });

        stockChart.render();
    }

    // Delay supaya sidebar selesai animasi
    setTimeout(initCharts, 600);

    // Resize stabil
    window.addEventListener("resize", function () {
        if (revenueChart) revenueChart.updateOptions({});
        if (stockChart) stockChart.updateOptions({});
    });

});



</script>

@endsection
