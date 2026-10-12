@extends('layouts.admin')

@section('title', 'Edit Resep')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="p-2 lg:p-4 max-w-[1200px] mx-auto space-y-6" style="font-family: 'Inter', sans-serif;">
    
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <nav class="flex mb-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                <span>Inventory</span>
                <span class="mx-2">/</span>
                <span>Recipes</span>
                <span class="mx-2">/</span>
                <span class="text-black">Edit</span>
            </nav>
            <h1 class="text-3xl font-bold text-black tracking-tight">Edit Resep</h1>
            <p class="text-sm text-gray-500 mt-1">Ubah komposisi bahan baku untuk menu: <strong>{{ $product->name }}</strong></p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recipes.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-50 border border-gray-200 text-slate-700 text-sm font-semibold rounded-md hover:bg-white transition-all shadow-sm active:scale-95">
                <span class="iconify text-lg" data-icon="lucide:arrow-left"></span>
                Kembali
            </a>
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

    <div class="bg-gray-50 rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('admin.recipes.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Header Form -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-semibold text-black">Informasi Resep</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Ubah komposisi bahan baku untuk menu ini</p>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Menu</label>
                    <div class="px-4 py-2.5 bg-slate-100 border border-gray-200 rounded-md text-slate-700 font-medium">
                        {{ $product->name }}
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-black flex items-center gap-2">
                            <span class="iconify text-black" data-icon="lucide:package"></span>
                            Komposisi Bahan Baku
                        </h3>
                        <button type="button" id="add-row"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 text-black text-xs font-bold rounded-md hover:bg-gray-50 border border-gray-200 transition-all">
                            <span class="iconify text-sm" data-icon="lucide:plus"></span>
                            Tambah Bahan
                        </button>
                    </div>
                    
                    <div id="items-container" class="space-y-3">
                        @foreach($product->recipeItems as $index => $item)
                        <div class="item-row grid grid-cols-12 gap-3 items-start bg-white p-3 rounded-md border border-gray-200">
                            <div class="col-span-5">
                                <select name="items[{{ $index }}][ingredient_id]" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-gray-200 focus:ring-4 focus:ring-[#1677ff]/10 transition-all" required>
                                    <option value="">Pilih Bahan Baku</option>
                                    @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" {{ $item->ingredient_id == $ingredient->id ? 'selected' : '' }}>
                                        {{ $ingredient->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-3">
                                <input type="number" step="0.01" name="items[{{ $index }}][quantity]" 
                                    class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-gray-200 focus:ring-4 focus:ring-[#1677ff]/10 transition-all"
                                    placeholder="Jumlah" value="{{ $item->quantity }}" required>
                            </div>
                            <div class="col-span-3">
                                <select name="items[{{ $index }}][unit_id]" class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-gray-200 focus:ring-4 focus:ring-[#1677ff]/10 transition-all">
                                    <option value="">Pilih Satuan</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-1">
                                <button type="button" class="remove-row w-full py-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-md transition-all">
                                    <span class="iconify text-lg" data-icon="lucide:trash-2"></span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-3">* Minimal 1 bahan baku untuk setiap resep</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 px-4 py-2 bg-white border-t border-gray-200">
                <a href="{{ route('admin.recipes.index') }}"
                    class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-white transition-all">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-black rounded-md hover:bg-gray-800 transition-all shadow-sm active:scale-95">
                    <span class="iconify text-base mr-1" data-icon="lucide:save"></span>
                    Update Resep
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let rowIndex = {{ $product->recipeItems->count() }};
    
    document.getElementById('add-row').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        
        newRow.querySelectorAll('input, select').forEach(el => el.value = '');
        
        newRow.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[(\d+)\]/, '[' + rowIndex + ']');
        });
        
        const removeBtn = newRow.querySelector('.remove-row');
        removeBtn.replaceWith(removeBtn.cloneNode(true));
        newRow.querySelector('.remove-row').addEventListener('click', function() {
            if(document.querySelectorAll('.item-row').length > 1) {
                newRow.remove();
            } else {
                alert('Minimal 1 bahan baku');
            }
        });
        
        container.appendChild(newRow);
        rowIndex++;
    });
    
    document.querySelectorAll('.remove-row').forEach(btn => {
        btn.addEventListener('click', function() {
            if(document.querySelectorAll('.item-row').length > 1) {
                btn.closest('.item-row').remove();
            } else {
                alert('Minimal 1 bahan baku');
            }
        });
    });
</script>
@endsection
