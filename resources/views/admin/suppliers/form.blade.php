@extends('layouts.admin')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <h2 class="text-xl font-semibold text-gray-800 mb-6">
        {{ isset($supplier) ? '✏️ Edit Supplier' : '➕ Tambah Supplier' }}
    </h2>

    <form action="{{ isset($supplier) ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}"
        method="POST" class="space-y-5">
        @csrf
        @if(isset($supplier))
        @method('PUT')
        @endif

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
            <input type="text" name="name"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                value="{{ old('name', $supplier->name ?? '') }}" required>
            @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <input type="text" name="address"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                value="{{ old('address', $supplier->address ?? '') }}">
            @error('address')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
            <input type="text" name="phone"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                value="{{ old('phone', $supplier->phone ?? '') }}">
            @error('phone')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Type -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Supplier</label>
            <input type="text" name="type"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                value="{{ old('type', $supplier->type ?? '') }}" placeholder="Depo Bangunan / Toko Material / dll">
            @error('type')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                {{ isset($supplier) ? 'Update' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.suppliers.index') }}"
                class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Kembali
            </a>
        </div>

    </form>
</div>
@endsection
