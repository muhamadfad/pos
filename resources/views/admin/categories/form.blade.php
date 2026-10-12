@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-50 border border-gray-200 text-gray-500 hover:bg-white hover:text-black transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-black">
                {{ isset($category) ? 'Edit Kategori' : 'Tambah Kategori' }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ isset($category) ? 'Perbarui nama kategori' : 'Buat kategori produk baru' }}
            </p>
        </div>
    </div>

    <!-- Card Form -->
    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm p-6">

        @if ($errors->any())
        <div
            class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-md px-4 py-3 mb-5 text-sm">
            <span class="iconify mt-0.5 shrink-0 text-red-500" data-icon="mdi:alert-circle-outline"></span>
            <span>{{ implode(', ', $errors->all()) }}</span>
        </div>
        @endif

        <form
            action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
            method="POST" class="space-y-5">
            @csrf
            @if(isset($category))
            @method('PUT')
            @endif

            <!-- Nama Kategori -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Kategori
                    <span class="text-red-500 ml-0.5">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-500">
                        <span class="iconify" data-icon="mdi:shape-outline"></span>
                    </div>
                    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
                        placeholder="Contoh: Makanan, Minuman" autofocus class="w-full pl-10 pr-4 py-2.5 text-sm border rounded-md transition
                               {{ $errors->has('name') ? 'border-red-400 bg-red-50 focus:ring-red-300' : 'border-slate-300 focus:ring-black/5 focus:border-gray-200' }}
                               focus:outline-none focus:ring-2">
                </div>
                @error('name')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-slate-100 hover:bg-slate-200 rounded-md transition">
                    <span class="iconify" data-icon="mdi:arrow-left"></span>
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-black hover:bg-gray-800 rounded-md transition shadow-sm">
                    <span class="iconify" data-icon="mdi:content-save-outline"></span>
                    {{ isset($category) ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
