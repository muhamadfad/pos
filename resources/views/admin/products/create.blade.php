@extends('layouts.admin')

@section('title', isset($isEdit) && $isEdit ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="max-w-[1100px] mx-auto space-y-6 pb-20" style="font-family: 'Inter', sans-serif;">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" 
               class="flex items-center justify-center w-10 h-10 bg-gray-50 border border-gray-200 rounded-md text-gray-500 hover:bg-white transition-all shadow-sm">
                <span class="iconify text-xl" data-icon="lucide:arrow-left"></span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-black tracking-tight">
                    {{ isset($isEdit) && $isEdit ? 'Edit Menu' : 'Tambah Menu Baru' }}
                </h1>
                <p class="text-sm text-gray-500 font-medium">Lengkapi detail informasi daftar menu Anda.</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-100 rounded-md flex gap-3">
        <span class="iconify text-rose-500 text-xl shrink-0" data-icon="lucide:alert-circle"></span>
        <div class="text-sm">
            <h4 class="font-bold text-rose-800">Ada kendala pada input Anda:</h4>
            <ul class="text-rose-700/80 list-disc list-inside mt-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ isset($isEdit) && $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" 
          method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @if(isset($isEdit) && $isEdit) @method('PUT') @endif

        <div class="lg:col-span-8 space-y-6">
            <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-200 bg-white">
                    <h3 class="text-sm font-semibold text-black">Informasi Menu</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Menu</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2.5 text-sm font-medium bg-gray-50 border border-gray-200 rounded-md focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all text-black placeholder:text-gray-500"
                            placeholder="Contoh: Espresso, Roti, Es Teh" value="{{ old('name', $product->name ?? '') }}">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Gambar Menu <span class="text-gray-500 normal-case font-normal">(Opsional)</span></label>
                        @if(isset($isEdit) && $isEdit && $product->image)
                        <div class="mb-3">
                            <img src="{{ asset('pos-images/' . basename($product->image)) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-md border border-gray-200">
                        </div>
                        @endif
                        <input type="file" id="image" name="image" accept="image/*"
                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-black hover:file:bg-gray-50 cursor-pointer">
                        <p class="text-[10px] text-gray-500">Maksimal 2MB. Format JPG, PNG, WEBP.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</label>
                            <select name="category_id" required
                                class="w-full px-4 py-2.5 text-sm font-medium bg-gray-50 border border-gray-200 rounded-md focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all text-slate-700 cursor-pointer appearance-none">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</label>
                            <select name="unit_id"
                                class="w-full px-4 py-2.5 text-sm font-medium bg-gray-50 border border-gray-200 rounded-md focus:ring-4 focus:ring-[#1677ff]/10 focus:border-gray-200 transition-all text-slate-700 cursor-pointer appearance-none">
                                <option value="">Pilih Satuan</option>
                                @foreach ($units ?? [] as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->abbreviation }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden sticky top-6">
                <div class="px-4 py-2 border-b border-gray-200 bg-white">
                    <h3 class="text-sm font-semibold text-black text-center">Harga Jual</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-black uppercase tracking-wider">Harga Jual</label>
                        <div class="relative group">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500">Rp</span>
                            <input type="number" id="selling_price" name="selling_price" required
                                class="w-full pl-10 pr-4 py-2.5 text-sm font-semibold bg-gray-50 border border-gray-200 rounded-md focus:ring-4 focus:ring-black/5 focus:border-gray-200 transition-all text-black"
                                placeholder="0" value="{{ old('selling_price', $product->selling_price ?? '') }}">
                        </div>
                    </div>

                    <div class="pt-4 space-y-3">
                        <button type="submit"
                            class="w-full py-3 bg-black text-white text-sm font-bold rounded-md hover:bg-gray-800 shadow-sm hover: transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span class="iconify text-lg" data-icon="{{ isset($isEdit) && $isEdit ? 'lucide:save' : 'lucide:check-circle' }}"></span>
                            {{ isset($isEdit) && $isEdit ? 'Simpan Perubahan' : 'Terbitkan Menu' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="w-full py-3 bg-gray-50 text-gray-500 text-sm font-semibold rounded-md border border-gray-200 hover:bg-white transition-all flex items-center justify-center">
                            Batalkan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
