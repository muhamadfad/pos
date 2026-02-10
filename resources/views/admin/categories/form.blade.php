@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border">

        <!-- Header -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
            {{ isset($category) ? '✏️ Edit Kategori' : '➕ Tambah Kategori' }}
        </h2>

        <!-- Error -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <!-- Form -->
        <form 
            action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" 
            method="POST" 
            class="space-y-5"
        >
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori
                </label>
                <input 
                    type="text" 
                    name="name"
                    value="{{ old('name', $category->name ?? '') }}"
                    class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-300 focus:border-blue-500"
                    placeholder="Contoh: Semen, Cat, Paku"
                    required
                >
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-between pt-4">
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg
                          hover:bg-gray-600 transition">
                    ↩️ Kembali
                </a>

                <button type="submit"
                    class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg
                           hover:bg-blue-700 transition">
                    {{ isset($category) ? '💾 Update' : '💾 Simpan' }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
