@extends('layouts.admin')

@section('title', 'Detail Cabang')

@section('content')
<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('super.branches.index') }}" class="w-10 h-10 bg-gray-50 rounded-md shadow-sm border border-gray-200 flex items-center justify-center text-gray-500 hover:text-black hover:border-gray-200 transition-colors">
            <span class="iconify text-xl" data-icon="mdi:arrow-left"></span>
        </a>
        <h1 class="text-2xl font-semibold text-black tracking-tight">Detail Cabang</h1>
    </div>

    <!-- Branch Header -->
    <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-lg p-8 text-white shadow-sm overflow-hidden group">
        <div class="absolute -right-8 -top-8 w-48 h-48 bg-gray-50/10 rounded-md blur-2xl group-hover:scale-110 transition-transform duration-500"></div>
        <div class="absolute right-10 bottom-0 w-32 h-32 bg-white border border-gray-2000/20 rounded-md blur-xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-semibold tracking-tight">{{ $branch->name }}</h1>
                    <span class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest {{ $branch->is_active ? 'bg-white border border-gray-2000/20 text-emerald-100 border border-gray-200' : 'bg-gray-50/10 text-white/70 border border-white/20' }}">
                        {{ $branch->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50/10 rounded-md backdrop-blur-md border border-white/10 text-sm font-medium text-indigo-50 mb-6">
                    <span class="iconify" data-icon="mdi:barcode-scan"></span> Kode: {{ $branch->code }}
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                    <div class="flex items-center gap-3 text-indigo-100 font-medium text-sm">
                        <div class="w-10 h-10 rounded-md bg-gray-50/10 flex items-center justify-center backdrop-blur-md">
                            <span class="iconify text-lg" data-icon="mdi:phone"></span>
                        </div>
                        <span>{{ $branch->phone ?? 'Belum ada telepon' }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-indigo-100 font-medium text-sm">
                        <div class="w-10 h-10 rounded-md bg-gray-50/10 flex items-center justify-center backdrop-blur-md">
                            <span class="iconify text-lg" data-icon="mdi:map-marker"></span>
                        </div>
                        <span class="max-w-xs">{{ $branch->address ?? 'Alamat belum diisi' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="shrink-0 flex gap-3">
                <a href="{{ route('super.branches.edit', $branch->id) }}" class="px-4 py-2 bg-gray-50 text-black font-bold rounded-md shadow-sm hover:bg-white transition-colors flex items-center gap-2 text-sm">
                    <span class="iconify" data-icon="mdi:pencil"></span> Edit Cabang
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="relative overflow-hidden bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-md opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.2em] mb-4 relative">Total User</p>
            <div class="flex items-end justify-between relative">
                <span class="text-3xl font-semibold text-black tracking-tight">{{ $stats['users']->count() }}</span>
                <div class="w-12 h-12 rounded-md bg-gray-50 flex items-center justify-center text-black">
                    <span class="iconify text-2xl" data-icon="mdi:account-group"></span>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white border border-gray-200 rounded-md opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.2em] mb-4 relative">Total Transaksi</p>
            <div class="flex items-end justify-between relative">
                <span class="text-3xl font-semibold text-black tracking-tight">{{ number_format($stats['recent_orders']->count() + ($stats['total_orders'] ?? 0), 0, ',', '.') }}</span>
                <div class="w-12 h-12 rounded-md bg-gray-50 border border-gray-200 flex items-center justify-center text-black">
                    <span class="iconify text-2xl" data-icon="mdi:receipt"></span>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white border border-gray-200 rounded-md opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.2em] mb-4 relative">Total Revenue</p>
            <div class="flex items-end justify-between relative">
                <span class="text-3xl font-semibold text-black tracking-tight flex items-baseline gap-1"><span class="text-sm text-gray-500">Rp</span>{{ number_format($stats['recent_orders']->sum('total_amount'), 0, ',', '.') }}</span>
                <div class="w-12 h-12 rounded-md bg-gray-50 border border-gray-200 flex items-center justify-center text-black shrink-0">
                    <span class="iconify text-2xl" data-icon="mdi:cash-multiple"></span>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-md opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-[0.2em] mb-4 relative">Stok Menipis</p>
            <div class="flex items-end justify-between relative">
                <span class="text-3xl font-semibold text-black tracking-tight">{{ $stats['low_stock_count'] ?? 0 }}</span>
                <div class="w-12 h-12 rounded-md bg-gray-50 flex items-center justify-center text-black">
                    <span class="iconify text-2xl" data-icon="mdi:alert-outline"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Left (2/3) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recent Orders -->
            <div class="bg-gray-50 rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-white">
                    <h3 class="font-semibold text-lg text-black tracking-tight flex items-center gap-2">
                        <span class="iconify text-black text-xl" data-icon="mdi:clock-outline"></span>
                        Transaksi Terbaru
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-gray-500 uppercase tracking-widest bg-white">
                            <tr>
                                <th class="px-4 py-2 font-bold">ID Transaksi</th>
                                <th class="px-4 py-2 font-bold">Customer</th>
                                <th class="px-4 py-2 font-bold">Total</th>
                                <th class="px-4 py-2 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($stats['recent_orders'] as $order)
                            <tr class="hover:bg-white transition-colors">
                                <td class="px-4 py-2">
                                    <div class="font-bold text-black">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-600">
                                    {{ $order->customer->name ?? 'Umum' }}
                                </td>
                                <td class="px-4 py-2 font-semibold text-black">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-widest {{ $order->payment_status == 'paid' ? 'bg-gray-50 border border-gray-200 text-black' : 'bg-gray-50 text-black' }}">
                                        {{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-md bg-slate-100 text-gray-500 mb-4">
                                        <span class="iconify text-2xl" data-icon="mdi:receipt-text-outline"></span>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada transaksi</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Users in this Branch -->
            <div class="bg-gray-50 rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-white">
                    <h3 class="font-semibold text-lg text-black tracking-tight flex items-center gap-2">
                        <span class="iconify text-black text-xl" data-icon="mdi:account-group-outline"></span>
                        User Cabang Ini
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-gray-500 uppercase tracking-widest bg-white">
                            <tr>
                                <th class="px-4 py-2 font-bold">User</th>
                                <th class="px-4 py-2 font-bold">Role</th>
                                <th class="px-4 py-2 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($stats['users'] as $user)
                            <tr class="hover:bg-white transition-colors">
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-md bg-gray-50 text-black flex items-center justify-center font-bold shrink-0">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-black">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-widest 
                                        {{ $user->role == 'super_admin' ? 'bg-gray-50 border border-gray-200 text-black' : 
                                           ($user->role == 'admin' ? 'bg-gray-50 border border-gray-200 text-black' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="flex items-center gap-1.5 text-xs font-bold {{ $user->is_active ? 'text-black' : 'text-red-500' }}">
                                        <span class="w-2 h-2 rounded-md {{ $user->is_active ? 'bg-white border border-gray-2000' : 'bg-red-500' }}"></span>
                                        {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    Belum ada user di cabang ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Right (1/3) -->
        <div class="space-y-8">
            <!-- Top Products -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-white">
                    <h3 class="font-semibold text-lg text-black tracking-tight flex items-center gap-2">
                        <span class="iconify text-black text-xl" data-icon="mdi:fire"></span>
                        Produk Terlaris
                    </h3>
                </div>
                <div class="p-6">
                    @if($stats['top_products']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['top_products'] as $index => $product)
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center font-semibold text-sm shrink-0
                                {{ $index == 0 ? 'bg-gray-50 text-black' : 
                                  ($index == 1 ? 'bg-slate-100 text-gray-600' : 
                                  ($index == 2 ? 'bg-orange-100 text-orange-600' : 'bg-white text-gray-500')) }}">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-black truncate">{{ $product->name }}</p>
                                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">{{ $product->total_sold }} Terjual</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-white rounded-md flex items-center justify-center mx-auto mb-3">
                            <span class="iconify text-slate-300 text-xl" data-icon="mdi:package-variant"></span>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Belum ada data penjualan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
