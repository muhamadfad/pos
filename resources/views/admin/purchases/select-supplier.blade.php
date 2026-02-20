@extends('layouts.admin')

@section('title', 'Pilih Supplier')

@section('content')
<div x-data="{ search: '' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pilih Supplier</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tentukan supplier untuk transaksi pembelian ini</p>
        </div>
        <a href="{{ route('admin.purchases.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-6 relative group">
        <div
            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-slate-400">
            <span class="iconify text-xl" data-icon="mdi:magnify"></span>
        </div>
        <input type="text" x-model="search"
            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm"
            placeholder="Cari nama supplier...">
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead
                    class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-widest border-b border-slate-200 text-center">
                    <tr>
                        <th class="px-6 py-4 text-left">Nama Supplier</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Alamat</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 border-b border-slate-100">
                    @forelse ($suppliers as $supplier)
                    <tr class="hover:bg-slate-50/80 transition-colors"
                        x-show="search === '' || '{{ strtolower($supplier->name) }}'.includes(search.toLowerCase())">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $supplier->name }}</div>
                            <div class="text-[12px] text-slate-500">ID: #SUP-{{ str_pad($supplier->id, 4, '0',
                                STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-medium">
                                <span class="iconify text-slate-400" data-icon="mdi:phone"></span>
                                {{ $supplier->phone ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                            {{ $supplier->address ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('admin.purchases.per-supplier', $supplier->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all font-bold group">
                                Pilih Supplier
                                <span class="iconify group-hover:translate-x-1 transition-transform"
                                    data-icon="mdi:arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center text-slate-500">
                            Belum ada data supplier.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
