@extends('layouts.admin')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-sm border">

    <h2 class="text-2xl font-semibold mb-6">➕ Tambah Barang Masuk</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ implode(', ', $errors->all()) }}
        </div>
    @endif

    <form action="{{ route('admin.transactions.in.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Pilih Produk -->
        <div>
            <label class="block font-semibold mb-1">Produk</label>
            <select name="product_id" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300" required>
                <option value="">-- Pilih Produk --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Quantity -->
        <div>
            <label class="block font-semibold mb-1">Jumlah Masuk</label>
            <input type="number" name="quantity" min="1"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                   placeholder="Jumlah barang masuk" required>
        </div>

        <!-- Tanggal -->
        <div>
            <label class="block font-semibold mb-1">Tanggal</label>
            <input type="date" name="date"
                   class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                   value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                💾 Simpan
            </button>
            <a href="{{ route('admin.transactions.in.index') }}"
               class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                ↩ Kembali
            </a>
        </div>
    </form>

</div>
@endsection
