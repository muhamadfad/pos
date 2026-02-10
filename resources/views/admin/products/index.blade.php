@extends('layouts.admin')

@section('title', 'Daftar Barang')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">📦 Daftar Barang</h2>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border rounded-lg overflow-hidden">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Barcode</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Harga Beli</th>
                    <th class="px-4 py-3">Harga Jual</th>
                    <th class="px-4 py-3">Stok</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $product->barcode }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                    <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-orange-600 font-medium">
                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-green-600 font-medium">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $product->stock <= 5 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="text-blue-600 hover:underline text-sm">Edit</a>

                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:underline text-sm">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        Data produk belum tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
