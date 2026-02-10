@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Heading -->
    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Total Produk -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-sm text-gray-500">Total Barang</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-sm text-gray-500">Stok Menipis</p>
            <p class="mt-2 text-3xl font-bold {{ $lowStock > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $lowStock }}
            </p>
        </div>

        <!-- Total User -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <p class="text-sm text-gray-500">Total User</p>
            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
        </div>

    </div>

</div>
@endsection
