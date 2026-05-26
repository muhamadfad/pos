@extends('layouts.admin')

@section('title', 'Manajemen Cabang')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800"><span class="iconify inline-block" data-icon="mdi:store-outline"></span> Manajemen Cabang</h1>
            <p class="text-slate-500 mt-1">Kelola semua cabang restoran Anda</p>
        </div>
        <a href="{{ route('super.branches.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <span class="iconify" data-icon="mdi:plus-circle"></span>
            Tambah Cabang
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('super.branches.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama, kode, atau telepon..." 
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="w-48">
                <select name="status" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Filter
                </button>
                <a href="{{ route('super.branches.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
        {{ session('error') }}
    </div>
    @endif

    <!-- Branches Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($branches as $branch)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
            <!-- Header Cabang -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-5 py-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-white font-bold text-lg">{{ $branch->name }}</h3>
                        <p class="text-indigo-100 text-sm">Kode: {{ $branch->code }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $branch->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                        {{ $branch->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
            </div>
            
            <!-- Body Cabang -->
            <div class="p-5">
                <!-- Kontak -->
                <div class="space-y-2 mb-4">
                    @if($branch->phone)
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="iconify text-slate-400" data-icon="mdi:phone"></span>
                        <span class="text-sm">{{ $branch->phone }}</span>
                    </div>
                    @endif
                    @if($branch->address)
                    <div class="flex items-start gap-2 text-slate-600">
                        <span class="iconify text-slate-400 mt-0.5" data-icon="mdi:map-marker"></span>
                        <span class="text-sm">{{ Str::limit($branch->address, 60) }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Statistik -->
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="bg-slate-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $branch->stats['total_users'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500">User</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format(($branch->stats['total_revenue'] ?? 0), 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500">Revenue</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $branch->stats['total_orders'] ?? 0 }}</p>
                        <p class="text-xs text-slate-500">Transaksi</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold {{ ($branch->stats['low_stock_count'] ?? 0) > 0 ? 'text-amber-600' : 'text-green-600' }}">
                            {{ $branch->stats['low_stock_count'] ?? 0 }}
                        </p>
                        <p class="text-xs text-slate-500">Stok Tipis</p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('super.branches.show', $branch->id) }}" 
                       class="flex-1 bg-indigo-50 text-indigo-600 text-center px-3 py-2 rounded-lg hover:bg-indigo-100 transition text-sm font-medium">
                        Detail
                    </a>
                    <a href="{{ route('super.branches.edit', $branch->id) }}" 
                       class="flex-1 bg-amber-50 text-amber-600 text-center px-3 py-2 rounded-lg hover:bg-amber-100 transition text-sm font-medium">
                        Edit
                    </a>
                    <button onclick="toggleStatus({{ $branch->id }})" 
                            class="px-3 py-2 rounded-lg text-sm font-medium {{ $branch->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                        {{ $branch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl">
            <span class="iconify text-6xl text-slate-300 mb-4" data-icon="mdi:store-off"></span>
            <p class="text-slate-500">Belum ada cabang. Silakan tambah cabang pertama Anda.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $branches->withQueryString()->links() }}
    </div>
</div>

<!-- Form untuk toggle status -->
<form id="toggleStatusForm" method="POST" style="display: none;">
    @csrf
</form>

<script>
function toggleStatus(branchId) {
    const form = document.getElementById('toggleStatusForm');
    form.action = `/super/branches/${branchId}/toggle-status`;
    if (confirm('Ubah status cabang ini?')) {
        form.submit();
    }
}
</script>
@endsection