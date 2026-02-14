@extends('layouts.admin')

@section('title', isset($mitra) ? 'Edit Mitra' : 'Tambah Mitra')

@section('content')
<div class="max-w-3xl bg-white p-6 rounded-xl shadow-sm border">

    <!-- Header -->
    <h2 class="text-xl font-semibold text-gray-800 mb-6">
        {{ isset($mitra) ? '✏️ Edit Mitra' : '➕ Tambah Mitra' }}
    </h2>

    <form 
        action="{{ isset($mitra) ? route('admin.mitra.update', $mitra) : route('admin.mitra.store') }}" 
        method="POST"
        class="space-y-5"
    >
        @csrf
        @if(isset($mitra))
            @method('PUT')
        @endif

        <!-- Nama Mitra -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mitra</label>
            <input type="text" name="nama_mitra"
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                   value="{{ old('nama_mitra', $mitra->nama_mitra ?? '') }}" required>
            @error('nama_mitra')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <input type="text" name="alamat"
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                   value="{{ old('alamat', $mitra->alamat ?? '') }}">
            @error('alamat')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- No HP -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
            <input type="text" name="no_hp"
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                   value="{{ old('no_hp', $mitra->no_hp ?? '') }}">
            @error('no_hp')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jenis Mitra -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Mitra</label>
            <input type="text" name="jenis_mitra"
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                   value="{{ old('jenis_mitra', $mitra->jenis_mitra ?? '') }}"
                   placeholder="Depo Bangunan / Toko Material / dll">
            @error('jenis_mitra')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-3 pt-4">
            <button type="submit"
                class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                {{ isset($mitra) ? 'Update' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.mitra.index') }}"
               class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Kembali
            </a>
        </div>

    </form>
</div>
@endsection
