@extends('layouts.admin')

@section('title', 'Kategori: ' . $category->name)

@section('content')
<div class="max-w-[1100px] mx-auto space-y-6 pb-10">

    <div class="flex flex-col md:flex-row md:items-center gap-4">
        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-gray-50 border border-gray-200 text-gray-500 hover:text-black hover:border-gray-200 hover:bg-gray-50 transition-all shadow-sm group">
            <span class="iconify text-xl group-active:scale-90 transition-transform" data-icon="lucide:chevron-left"></span>
        </a>
        
        <div class="flex-1">
            <nav class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Kategori</span>
                <span class="iconify text-slate-300 text-[10px]" data-icon="lucide:chevron-right"></span>
                <span class="text-[10px] font-bold text-black uppercase tracking-widest">{{ $category->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-black tracking-tight">{{ $category->name }}</h1>
        </div>

        <a href="{{ route('admin.categories.edit', $category) }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-black text-white text-sm font-bold rounded-md hover:bg-gray-800 transition-all shadow-sm active:scale-95">
            <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
            Edit Kategori
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-5 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Total Koleksi</p>
                <p class="text-3xl font-bold text-black tracking-tight">{{ $products->total() }}</p>
                <p class="text-[11px] text-gray-500 mt-1 font-medium">Produk terdaftar</p>
            </div>
            <span class="iconify absolute -right-2 -bottom-2 text-6xl text-slate-50 group-hover:text-indigo-50/50 transition-colors" data-icon="lucide:package"></span>
        </div>
    </div>

    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden transition-all">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <div>
                <h2 class="font-bold text-black tracking-tight flex items-center gap-2">
                    <span class="w-2 h-5 bg-black rounded-md"></span>
                    Daftar Produk dalam Kategori
                </h2>
            </div>
            <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-black bg-gray-50 hover:bg-gray-50 rounded-md transition-all active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:plus-circle"></span>
                Tambah Produk
            </a>
        </div>

        @if($products->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 bg-white">
            <div class="w-20 h-20 bg-gray-50 rounded-md shadow-sm border border-gray-200 flex items-center justify-center mb-4">
                <span class="iconify text-4xl text-slate-200" data-icon="lucide:package-search"></span>
            </div>
            <p class="text-sm font-bold text-gray-500">Belum ada produk</p>
            <p class="text-xs text-gray-500 mt-1">Kategori ini masih kosong.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest w-16 text-center">#</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Detail Produk</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Harga Jual</th>
                        <!-- removed stok column header -->
                        <th class="px-4 py-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="group hover:bg-white transition-colors">
                        <td class="px-4 py-2 text-center text-xs text-gray-500 font-mono">
                            {{ $products->firstItem() + $loop->index }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-black group-hover:text-black transition-colors">{{ $product->name }}</span>
                                <span class="text-[11px] text-gray-500 font-mono mt-0.5 tracking-tighter">{{ $product->barcode }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <span class="text-sm font-bold text-slate-700">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.products.show', $product) }}"
                                    class="p-2 text-gray-500 hover:text-gray-600 hover:bg-gray-50 rounded-md transition-all"
                                    title="Detail">
                                    <span class="iconify text-lg" data-icon="lucide:eye"></span>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="p-2 text-gray-500 hover:text-black hover:bg-gray-50 rounded-md transition-all"
                                    title="Edit">
                                    <span class="iconify text-lg" data-icon="lucide:edit-3"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white flex items-center justify-between">
            <p class="text-[11px] text-gray-500 font-medium">
                Showing <span class="text-black font-bold">{{ $products->firstItem() }}</span> to <span class="text-black font-bold">{{ $products->lastItem() }}</span>
            </p>
            <div class="flex items-center gap-1.5">
                {{-- Gunakan template pagination yang sudah kita samakan tadi --}}
                {{ $products->links('vendor.pagination.tailwind-custom') }}
            </div>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
