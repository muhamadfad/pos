@extends('layouts.admin')

@section('title', 'Manajemen Cabang')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-black tracking-tight flex items-center gap-3">
                <span class="p-2 bg-black text-white rounded-md shadow-sm">
                    <span class="iconify" data-icon="mdi:store-outline"></span>
                </span>
                Manajemen Cabang
            </h1>
            <p class="text-gray-500 font-medium tracking-tight">Kelola semua cabang restoran Anda</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('super.branches.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-md font-bold text-sm shadow-sm  hover:bg-gray-800 transition-all hover:scale-[1.02] active:scale-95">
                <span class="iconify text-lg" data-icon="mdi:plus-circle"></span>
                Tambah Cabang
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-white border border-gray-200 border border-gray-200 text-black px-4 py-2 rounded-md flex items-center gap-3">
        <span class="iconify text-2xl" data-icon="mdi:check-circle"></span>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-md flex items-center gap-3">
        <span class="iconify text-2xl" data-icon="mdi:alert-circle"></span>
        <span class="font-medium text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm p-6">
        <form method="GET" action="{{ route('super.branches.index') }}" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 group w-full">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 transition-colors group-focus-within:text-black">
                    <span class="iconify text-xl" data-icon="mdi:magnify"></span>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, kode, atau telepon..."
                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-sm font-medium focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all outline-none">
            </div>
            <div class="w-full md:w-48 relative">
                <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-sm font-medium focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all outline-none appearance-none">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <span class="iconify absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" data-icon="mdi:chevron-down"></span>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="px-4 py-2 bg-black text-white rounded-md font-bold text-sm shadow-sm  hover:bg-gray-800 transition-all w-full md:w-auto">
                    Filter
                </button>
                <a href="{{ route('super.branches.index') }}" class="px-4 py-2 bg-slate-100 text-gray-600 rounded-md font-bold text-sm hover:bg-slate-200 transition-all text-center w-full md:w-auto">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Branches Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($branches as $branch)
        <div class="relative bg-gray-50 rounded-lg border border-gray-200 shadow-sm hover:shadow-sm transition-all group overflow-hidden flex flex-col">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200 relative overflow-hidden bg-white">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-md opacity-50 group-hover:scale-110 transition-transform"></div>
                
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <h3 class="text-xl font-semibold text-black tracking-tight mb-1">{{ $branch->name }}</h3>
                        <p class="text-sm font-medium text-gray-500 flex items-center gap-1">
                            <span class="iconify" data-icon="mdi:barcode-scan"></span> {{ $branch->code }}
                        </p>
                    </div>
                    <span class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-widest {{ $branch->is_active ? 'bg-gray-50 border border-gray-200 text-black' : 'bg-slate-200 text-gray-600' }}">
                        {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6 flex-1 flex flex-col">
                <div class="space-y-3 mb-6">
                    @if($branch->phone)
                    <div class="flex items-center gap-3 text-gray-600 text-sm font-medium">
                        <div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center text-gray-500 shrink-0">
                            <span class="iconify" data-icon="mdi:phone"></span>
                        </div>
                        <span>{{ $branch->phone }}</span>
                    </div>
                    @endif
                    @if($branch->address)
                    <div class="flex items-center gap-3 text-gray-600 text-sm font-medium">
                        <div class="w-8 h-8 rounded-md bg-slate-100 flex items-center justify-center text-gray-500 shrink-0">
                            <span class="iconify" data-icon="mdi:map-marker"></span>
                        </div>
                        <span class="truncate">{{ Str::limit($branch->address, 40) }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Statistik -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-md p-4">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-1">Total User</p>
                        <p class="text-xl font-semibold text-black">{{ $branch->stats['total_users'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-md p-4">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-1">Revenue</p>
                        <p class="text-xl font-semibold text-black">{{ number_format(($branch->stats['total_revenue'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white rounded-md p-4">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-1">Transaksi</p>
                        <p class="text-xl font-semibold text-black">{{ $branch->stats['total_orders'] ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-md p-4">
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-1">Stok Tipis</p>
                        <p class="text-xl font-semibold {{ ($branch->stats['low_stock_count'] ?? 0) > 0 ? 'text-black' : 'text-black' }}">
                            {{ $branch->stats['low_stock_count'] ?? 0 }}
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="mt-auto flex gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('super.branches.show', $branch->id) }}" 
                       class="flex-1 bg-gray-50 text-black text-center px-4 py-2.5 rounded-md hover:bg-gray-50 transition-colors text-sm font-bold">
                        Detail
                    </a>
                    <a href="{{ route('super.branches.edit', $branch->id) }}" 
                       class="flex-1 bg-white text-slate-700 text-center px-4 py-2.5 rounded-md hover:bg-gray-50 transition-colors text-sm font-bold">
                        Edit
                    </a>
                    <button onclick="toggleStatus({{ $branch->id }})" 
                            class="px-4 py-2.5 rounded-md text-sm font-bold transition-colors {{ $branch->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-white border border-gray-200 text-black hover:bg-gray-50 border border-gray-200' }}">
                        {{ $branch->is_active ? 'Matikan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-gray-50 rounded-lg border border-gray-200">
            <div class="w-20 h-20 bg-white rounded-md flex items-center justify-center mx-auto mb-4">
                <span class="iconify text-4xl text-slate-300" data-icon="mdi:store-off"></span>
            </div>
            <h3 class="text-xl font-semibold text-black tracking-tight mb-2">Belum Ada Cabang</h3>
            <p class="text-gray-500 font-medium">Anda belum menambahkan cabang pertama Anda.</p>
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
