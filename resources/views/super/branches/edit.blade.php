@extends('layouts.admin')

@section('title', 'Edit Cabang')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super.branches.index') }}" class="text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 bg-slate-50">
            <h2 class="text-xl font-bold text-slate-800">Edit Cabang</h2>
            <p class="text-slate-500 text-sm mt-1">Perbarui informasi cabang</p>
        </div>

        <form method="POST" action="{{ route('super.branches.update', $branch->id) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Kode Cabang -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Kode Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $branch->code) }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-500 @enderror"
                           required>
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Cabang -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Nama Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $branch->name) }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telepon -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $branch->email) }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700">Aktifkan cabang ini</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-slate-200">
                <a href="{{ route('super.branches.index') }}" 
                   class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Update Cabang
                </button>
            </div>
        </form>

        <!-- Delete Branch -->
        <div class="border-t border-slate-200 px-6 py-4 bg-red-50">
            <h3 class="text-sm font-medium text-red-800 mb-2">Hapus Cabang</h3>
            <p class="text-xs text-red-600 mb-3">Tindakan ini tidak dapat dibatalkan. Cabang akan dihapus permanen.</p>
            <form method="POST" action="{{ route('super.branches.destroy', $branch->id) }}" onsubmit="return confirm('Yakin ingin menghapus cabang ini? Semua data terkait akan hilang!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                    Hapus Cabang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection