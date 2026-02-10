@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">

    <h1 class="text-2xl font-semibold text-gray-800">📊 Laporan Keuangan</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Pendapatan -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-sm text-gray-500">Pendapatan</p>
            <p class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-sm text-red-600 mt-1">Pengeluaran: Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>

        <!-- Bisa digandakan untuk Harian, Mingguan, Bulanan, dsb, tapi tetap pakai totalIncome/totalExpense -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-sm text-gray-500">Ringkasan</p>
            <p class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-sm text-red-600 mt-1">Pengeluaran: Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>

    </div>

</div>
@endsection
