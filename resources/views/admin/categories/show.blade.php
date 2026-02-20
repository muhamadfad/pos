@extends('layouts.admin')

@section('title', 'Kategori: ' . $category->name)

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-indigo-600">{{ $category->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $category->name }}</h1>
        </div>
        <a href="{{ route('admin.categories.edit', $category) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
            <span class="iconify" data-icon="mdi:pencil-outline"></span>
            Edit Kategori
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Produk</p>
            <p class="text-3xl font-bold text-slate-800">{{ $products->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Stok Habis</p>
            <p class="text-3xl font-bold text-red-500">
                {{ $category->products()->where('stock', 0)->count() }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 col-span-2 sm:col-span-1">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Stok Menipis (≤5)</p>
            <p class="text-3xl font-bold text-amber-500">
                {{ $category->products()->where('stock', '>', 0)->where('stock', '<=', 5)->count() }}
            </p>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <!-- Table Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <span class="iconify text-indigo-400" data-icon="mdi:package-variant-closed"></span>
                Daftar Produk
            </h2>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                <span class="iconify" data-icon="mdi:plus"></span>
                Tambah Produk
            </a>
        </div>

        @if($products->isEmpty())
        <div class="flex flex-col items-center gap-3 py-16 text-slate-400">
            <span class="iconify text-5xl" data-icon="mdi:package-variant-closed-remove"></span>
            <p class="font-medium">Belum ada produk di kategori ini</p>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                <span class="iconify" data-icon="mdi:plus"></span>
                Tambah Produk
            </a>
        </div>
        @else
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th
                        class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">
                        #</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Barcode</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama
                        Produk</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Harga Beli</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Harga Jual</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Stok</th>
                    <th
                        class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($products as $product)
                <tr class="hover:bg-slate-50 transition-colors {{ $product->stock == 0 ? 'bg-red-50/40' : '' }}">
                    <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-medium">
                        {{ $products->firstItem() + $loop->index }}
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded">{{
                            $product->barcode }}</span>
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $product->name }}</td>
                    <td class="px-5 py-3.5 text-right text-xs text-slate-500">
                        Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-slate-800">
                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($product->stock == 0)
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-600">
                            <span class="iconify" data-icon="mdi:alert-circle-outline"></span>
                            Habis
                        </span>
                        @elseif($product->stock <= 5) <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                            <span class="iconify" data-icon="mdi:alert-outline"></span>
                            {{ $product->stock }}
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                                {{ $product->stock }}
                            </span>
                            @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.products.show', $product) }}"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-500 bg-slate-100 hover:bg-slate-200 transition"
                                title="Detail">
                                <span class="iconify text-sm" data-icon="mdi:eye-outline"></span>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition"
                                title="Edit">
                                <span class="iconify text-sm" data-icon="mdi:pencil-outline"></span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $products->links() }}
        </div>
        @endif
        @endif

    </div>

</div>
@endsection
