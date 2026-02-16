@extends('layouts.admin')

@section('title', isset($customer) ? 'Edit Customer' : 'Tambah Customer')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-gray-200">

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
