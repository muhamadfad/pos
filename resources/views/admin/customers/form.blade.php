@extends('layouts.admin')

@section('title', isset($customer) ? 'Edit Customer' : 'Tambah Customer')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.customers.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                {{ isset($customer) ? 'Edit Customer' : 'Tambah Customer' }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ isset($customer) ? 'Perbarui data customer' : 'Daftarkan customer baru' }}
            </p>
        </div>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        @if($errors->any())
        <div
            class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
            <span class="iconify mt-0.5 shrink-0 text-red-500" data-icon="mdi:alert-circle-outline"></span>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form
            action="{{ isset($customer) ? route('admin.customers.update', $customer) : route('admin.customers.store') }}"
            method="POST" class="space-y-5">
            @csrf
            @if(isset($customer))
            @method('PUT')
            @endif

            <!-- Nama -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nama Customer <span class="text-red-500 ml-0.5">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:account-outline"></span>
                    </div>
                    <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
                        placeholder="Nama customer" autofocus class="w-full pl-10 pr-4 py-2.5 text-sm border rounded-xl transition
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
                    <div class="absolute top-3 left-0 flex items-start pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:map-marker-outline"></span>
                    </div>
                    <textarea name="address" rows="3" placeholder="Alamat lengkap"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">{{ old('address', $customer->address ?? '') }}</textarea>
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
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>
                @error('phone')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span> {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Jenis Customer -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Jenis Customer <span class="text-red-500 ml-0.5">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:tag-outline"></span>
                    </div>
                    <select name="type"
                        class="w-full pl-10 pr-10 py-2.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition appearance-none bg-white">
                        <option value="perorangan" {{ old('type', $customer->type ?? 'perorangan') === 'perorangan' ?
                            'selected' : '' }}>Perorangan</option>
                        <option value="toko" {{ old('type', $customer->type ?? '') === 'toko' ? 'selected' : '' }}>Toko
                        </option>
                        <option value="proyek" {{ old('type', $customer->type ?? '') === 'proyek' ? 'selected' : ''
                            }}>Proyek</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                        <span class="iconify" data-icon="mdi:chevron-down"></span>
                    </div>
                </div>
                @error('type')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <span class="iconify" data-icon="mdi:alert-circle-outline"></span> {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.customers.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    <span class="iconify" data-icon="mdi:arrow-left"></span>
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-md shadow-indigo-100">
                    <span class="iconify" data-icon="mdi:content-save-outline"></span>
                    {{ isset($customer) ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
        @if(isset($customer))
        <span class="iconify text-blue-600" data-icon="mdi:pencil"></span>
        Edit Customer
        @else
        <span class="iconify text-blue-600" data-icon="mdi:plus"></span>
        Tambah Customer
        @endif
    </h2>
</div>

@if($errors->any())
<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ isset($customer) ? route('admin.customers.update', $customer) : route('admin.customers.store') }}"
    method="POST" class="space-y-4">
    @csrf
    @if(isset($customer))
    @method('PUT')
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
        <textarea name="address" rows="3"
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Customer *</label>
        <select name="type" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            required>
            <option value="perorangan" {{ old('type', $customer->type ?? 'perorangan') === 'perorangan' ? 'selected'
                : '' }}>Perorangan</option>
            <option value="toko" {{ old('type', $customer->type ?? '') === 'toko' ? 'selected' : '' }}>Toko</option>
            <option value="proyek" {{ old('type', $customer->type ?? '') === 'proyek' ? 'selected' : '' }}>Proyek
            </option>
        </select>
    </div>

    <div class="flex gap-3 pt-4">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            💾 Simpan
        </button>
        <a href="{{ route('admin.customers.index') }}"
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            ← Kembali
        </a>
    </div>
</form>

</div>
@endsection
