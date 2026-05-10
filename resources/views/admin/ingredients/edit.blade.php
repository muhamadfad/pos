@extends('layouts.admin')

@section('title', 'Edit Bahan Baku')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="p-2 lg:p-4 max-w-[1200px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">
    
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <span>Inventory</span>
                <span class="mx-2">/</span>
                <span>Ingredients</span>
                <span class="mx-2">/</span>
                <span class="text-indigo-600">Edit</span>
            </nav>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Edit Bahan Baku</h1>
            <p class="text-sm text-slate-500 mt-1">Ubah informasi bahan baku yang sudah ada</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ingredients.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:arrow-left"></span>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('admin.ingredients.update', $ingredient->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Header Form -->
                <div class="border-b border-slate-200 pb-4">
                    <h2 class="text-lg font-semibold text-slate-900">Informasi Bahan Baku</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Lengkapi data bahan baku di bawah ini</p>
                </div>

                <!-- Row 1: Nama Bahan & Satuan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Bahan Baku <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $ingredient->name) }}" 
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all @error('name') border-rose-500 ring-rose-500/20 @enderror"
                               placeholder="Contoh: Tepung Terigu, Gula Pasir, Garam..."
                               required>
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Satuan
                        </label>
                        <select name="unit_id" 
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer @error('unit_id') border-rose-500 @enderror">
                            <option value="">Pilih Satuan</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $ingredient->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Stok Awal, Stok Minimal, Harga Beli -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Stok Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">📦</span>
                            <input type="number" 
                                   step="0.01" 
                                   name="current_stock" 
                                   value="{{ old('current_stock', $ingredient->current_stock) }}" 
                                   class="w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all @error('current_stock') border-rose-500 @enderror"
                                   placeholder="0"
                                   required>
                        </div>
                        @error('current_stock')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Stok Minimal <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">⚠️</span>
                            <input type="number" 
                                   step="0.01" 
                                   name="min_stock" 
                                   value="{{ old('min_stock', $ingredient->min_stock) }}" 
                                   class="w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all @error('min_stock') border-rose-500 @enderror"
                                   placeholder="0"
                                   required>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Peringatan jika stok di bawah nilai ini</p>
                        @error('min_stock')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Harga Beli
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">💰</span>
                            <input type="number" 
                                   step="0.01" 
                                   name="unit_cost" 
                                   value="{{ old('unit_cost', $ingredient->unit_cost) }}" 
                                   class="w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all @error('unit_cost') border-rose-500 @enderror"
                                   placeholder="0">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Harga beli per satuan</p>
                        @error('unit_cost')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 3: Barcode & Cabang -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Cabang
                        </label>
                        <select name="branch_id" 
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer @error('branch_id') border-rose-500 @enderror">
                            <option value="">Pilih Cabang (Pusat jika kosong)</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $ingredient->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status Active -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Status Aktif</p>
                        <p class="text-xs text-slate-500">Nonaktifkan jika bahan baku tidak digunakan sementara</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               class="sr-only peer"
                               {{ old('is_active', $ingredient->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50/50 border-t border-slate-200">
                <button type="button" 
                        onclick="window.location.href='{{ route('admin.ingredients.index') }}'"
                        class="px-6 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-all shadow-sm active:scale-95">
                    <span class="iconify text-base mr-1" data-icon="lucide:save"></span>
                    Update Bahan Baku
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0.5;
    }
</style>
@endsection