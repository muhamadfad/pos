@extends('layouts.admin')

@section('title', 'Laporan Harian')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">
    <h2 class="text-xl font-semibold mb-4">
        Laporan Keuangan Harian - {{ $today->format('d-m-Y') }}
    </h2>

    <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-green-100 p-4 rounded">
            <p class="text-gray-600">Pendapatan (Barang Keluar × Harga Jual)</p>
            <p class="text-2xl font-bold text-green-700">
                Rp {{ number_format($finance['income'], 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-red-100 p-4 rounded">
            <p class="text-gray-600">Biaya (Barang Keluar × Harga Beli)</p>
            <p class="text-2xl font-bold text-red-700">
                Rp {{ number_format($finance['cost'], 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-blue-100 p-4 rounded">
            <p class="text-gray-600">Laba / Rugi</p>
            <p class="text-2xl font-bold {{ $finance['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                Rp {{ number_format($finance['profit'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <h3 class="text-lg font-semibold mb-3">Barang Terlaris Hari Ini</h3>

    <canvas id="salesChart" height="120"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($topProducts->pluck('name')) !!};
    const data = {!! json_encode($topProducts->pluck('total_qty')) !!};

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Terjual',
                data: data,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
