@extends('layouts.admin')

@section('title', 'Detail Cabang')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('super.branches.index') }}" class="text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali ke Daftar Cabang
        </a>
    </div>

    <!-- Branch Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold">{{ $branch->name }}</h1>
                <p class="text-indigo-100">Kode: {{ $branch->code }}</p>
                <div class="mt-3 flex gap-4">
                    <div class="flex items-center gap-2">
                        <span class="iconify" data-icon="mdi:phone"></span>
                        <span>{{ $branch->phone ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="iconify" data-icon="mdi:email"></span>
                        <span>{{ $branch->email ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $branch->is_active ? 'bg-green-500' : 'bg-gray-500' }}">
                {{ $branch->is_active ? 'AKTIF' : 'NONAKTIF' }}
            </span>
        </div>
        <div class="mt-4">
            <div class="flex items-start gap-2">
                <span class="iconify mt-0.5" data-icon="mdi:map-marker"></span>
                <span>{{ $branch->address ?? 'Alamat belum diisi' }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-indigo-500">
            <p class="text-slate-500 text-sm">Total User</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['users']->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
            <p class="text-slate-500 text-sm">Total Transaksi</p>
            <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['recent_orders']->count() + ($stats['total_orders'] ?? 0), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <p class="text-slate-500 text-sm">Total Revenue</p>
            <p class="text-3xl font-bold text-slate-800">Rp {{ number_format($stats['recent_orders']->sum('total_amount'), 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
            <p class="text-slate-500 text-sm">Stok Menipis</p>
            <p class="text-3xl font-bold text-amber-600">{{ $stats['low_stock_count'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow-sm mb-6 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50">
            <h3 class="font-bold text-slate-800">Produk Terlaris</h3>
        </div>
        <div class="p-6">
            @if($stats['top_products']->count() > 0)
            <div class="space-y-3">
                @foreach($stats['top_products'] as $product)
                <div class="flex justify-between items-center">
                    <span class="text-slate-700">{{ $product->name }}</span>
                    <span class="font-semibold text-indigo-600">{{ $product->total_sold }} terjual</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-slate-400 text-center">Belum ada data penjualan</p>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-sm mb-6 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Transaksi Terbaru</h3>
            <a href="#" class="text-indigo-600 text-sm hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($stats['recent_orders'] as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 text-sm">#{{ $order->id }}</td>
                        <td class="px-6 py-3 text-sm">{{ $order->customer->name ?? 'Umum' }}</td>
                        <td class="px-6 py-3 text-sm font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Users in this Branch -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">User di Cabang Ini</h3>
            <a href="{{ route('super.users.create') }}?branch_id={{ $branch->id }}" class="text-indigo-600 text-sm hover:underline">+ Tambah User</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($stats['users'] as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 text-sm">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-700' : 
                                   ($user->role == 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada user di cabang ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection