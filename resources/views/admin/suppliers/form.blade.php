@extends('layouts.admin')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.suppliers.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                {{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier' }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ isset($supplier) ? 'Perbarui data supplier' : 'Daftarkan supplier baru' }}
            </p>
        </div>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        @if ($errors->any())
        <div
            class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
            <span class="iconify mt-0.5 shrink-0 text-red-500" data-icon="mdi:alert-circle-outline"></span>
            <span>{{ implode(', ', $errors->all()) }}</span>
        </div>
        @endif

        <form
            action="{{ isset($supplier) ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}"
            method="POST" class="space-y-5">
            @csrf
            @if(isset($supplier))
            @method('PUT')
            @endif

            <!-- Nama -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Supplier <span class="text-red-500 ml-0.5">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:truck-outline"></span>
                    </div>
                    <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}"
                        placeholder="Nama supplier" autofocus class="w-full pl-10 pr-4 py-2.5 text-sm border rounded-xl transition
                               {{ $errors->has('name') ? 'border-red-400 bg-red-50 focus:ring-red-300' : 'border-slate-300 focus:ring-indigo-200 focus:border-indigo-400' }}
                               focus:outline-none focus:ring-2">
                </div>
                @error('name')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span> {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:map-marker-outline"></span>
                    </div>
                    <input type="text" name="address" value="{{ old('address', $supplier->address ?? '') }}"
                        placeholder="Alamat lengkap"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>
                @error('address')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span> {{ $message }}
                </p>
                @enderror
            </div>

            <!-- No HP -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">No HP</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:phone-outline"></span>
                    </div>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>
                @error('phone')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span> {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Jenis -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Supplier</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:tag-outline"></span>
                    </div>
                    <input type="text" name="type" value="{{ old('type', $supplier->type ?? '') }}"
                        placeholder="Depo Bangunan / Toko Material / dll"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>
                @error('type')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span> {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.suppliers.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    <span class="iconify" data-icon="mdi:arrow-left"></span>
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-md shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:content-save-outline"></span>
                    {{ isset($supplier) ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
