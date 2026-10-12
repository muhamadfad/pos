@extends('layouts.admin')

@section('title', 'Tambah Cabang Baru')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super.branches.index') }}" class="text-black hover:text-black flex items-center gap-1">
            <span class="iconify" data-icon="mdi:arrow-left"></span>
            Kembali
        </a>
    </div>

    <div class="bg-gray-50 rounded-md shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-4 py-2 bg-white">
            <h2 class="text-lg font-semibold text-black">Tambah Cabang Baru</h2>
            <p class="text-gray-500 text-sm mt-1">Isi informasi cabang baru di bawah ini</p>
        </div>

        <form method="POST" action="{{ route('super.branches.store') }}" class="p-6">
            @csrf

            <div class="space-y-5">
                <!-- Kode Cabang -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Kode Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-[#1677ff] focus:border-gray-200 @error('code') border-red-500 @enderror"
                           placeholder="CB001" required>
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Kode unik untuk identifikasi cabang</p>
                </div>

                <!-- Nama Cabang -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Nama Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-[#1677ff] focus:border-gray-200 @error('name') border-red-500 @enderror"
                           placeholder="Cabang Utama" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telepon -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-[#1677ff] focus:border-gray-200"
                           placeholder="081234567890">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-[#1677ff] focus:border-gray-200"
                           placeholder="cabang@resto.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-4 py-2 border border-slate-300 rounded-md focus:ring-[#1677ff] focus:border-gray-200"
                              placeholder="Jl. Contoh No. 123, Kota">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="rounded border-slate-300 text-black focus:ring-[#1677ff]">
                        <span class="text-sm text-slate-700">Aktifkan cabang ini</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-gray-200">
                <a href="{{ route('super.branches.index') }}" 
                   class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-white transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-black text-white rounded-md hover:bg-gray-800 transition">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
