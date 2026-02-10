@extends('layouts.admin')

@section('title', 'Tambah Barang Keluar')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-bold mb-6">➕ Tambah Barang Keluar</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg">
            {{ implode(', ', $errors->all()) }}
        </div>
    @endif

    <form action="{{ route('admin.transactions.out.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Produk -->
        <div>
            <label class="block font-semibold mb-1">Produk</label>
            <select name="product_id" required
                    class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300">
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} (Stok: {{ $product->stock }})
                    </option>
                @endforeach
            </select>
            @error('product_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Jumlah -->
        <div>
            <label class="block font-semibold mb-1">Jumlah Keluar</label>
            <input type="number" name="quantity" min="1"
                   value="{{ old('quantity') }}"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                   required>
            @error('quantity') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Tanggal -->
        <div>
            <label class="block font-semibold mb-1">Tanggal</label>
            <input type="date" name="date"
                   value="{{ old('date', date('Y-m-d')) }}"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                   required>
            @error('date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                💾 Simpan
            </button>
            <a href="{{ route('admin.transactions.out.index') }}"
               class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                ↩ Kembali
            </a>
        </div>

    </form>
</div>
@endsection
