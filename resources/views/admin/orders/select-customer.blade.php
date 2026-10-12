@extends('layouts.admin')

@section('title', 'Pilih Customer')

@section('content')
<div x-data="{ search: '' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-black">Pilih Customer</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tentukan customer untuk transaksi penjualan ini</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all text-sm font-semibold shadow-sm">
                <span class="iconify" data-icon="mdi:arrow-left"></span>
                Kembali
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6 relative group">
        <div
            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-black text-gray-500">
            <span class="iconify text-xl" data-icon="mdi:magnify"></span>
        </div>
        <input type="text" x-model="search"
            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-slate-700 placeholder:text-gray-500 focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all shadow-sm"
            placeholder="Cari nama customer (toko / proyek)...">
    </div>

    <!-- Table Card -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead
                    class="bg-white text-gray-600 uppercase text-[11px] font-bold tracking-widest border-b border-gray-200 text-center">
                    <tr>
                        <th class="px-4 py-2 text-left">Pelanggan</th>
                        <th class="px-4 py-2">Tipe</th>
                        <th class="px-4 py-2">Kontak</th>
                        <th class="px-4 py-2">Alamat</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 border-b border-gray-200">
                    @forelse ($customers as $customer)
                    <tr class="hover:bg-white transition-colors"
                        x-show="search === '' || '{{ strtolower($customer->name) }}'.includes(search.toLowerCase())">
                        <td class="px-4 py-2">
                            <div class="font-bold text-black">{{ $customer->name }}</div>
                            <div class="text-[12px] text-gray-500">ID: #CUS-{{ str_pad($customer->id, 4, '0',
                                STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-4 py-2 text-center">
                            @php
                            $typeCol = match($customer->type) {
                            'store' => 'bg-white border border-gray-200 text-black border-gray-200',
                            'project' => 'bg-white border border-gray-200 text-black border-gray-200',
                            'retail' => 'bg-white text-gray-600 border-gray-200',
                            default => 'bg-gray-50 text-gray-600 border-gray-100'
                            };
                            $typeLabel = match($customer->type) {
                            'store' => 'Toko',
                            'project' => 'Proyek',
                            'retail' => 'Eceran',
                            default => 'Umum'
                            };
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-md text-[10px] font-bold border uppercase tracking-wider {{ $typeCol }}">
                                {{ $typeLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-gray-600 rounded-md text-[12px] font-medium">
                                <span class="iconify text-gray-500" data-icon="mdi:phone"></span>
                                {{ $customer->phone ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2 text-gray-600 max-w-xs truncate">
                            {{ $customer->address ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-center whitespace-nowrap text-right">
                            <a href="{{ route('admin.orders.per-customer', $customer->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 transition-all font-bold group">
                                Buat Pesanan
                                <span class="iconify group-hover:translate-x-1 transition-transform"
                                    data-icon="mdi:arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-gray-500">
                            Belum ada data customer.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
