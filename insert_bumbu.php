<?php

use App\Models\Unit;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\RecipeItem;

// 1. Create Units
$unitBks = Unit::firstOrCreate(['name' => 'Bungkus'], ['abbreviation' => 'Bks']);
$unitIkat = Unit::firstOrCreate(['name' => 'Ikat'], ['abbreviation' => 'Ikat']);
$unitL = Unit::firstOrCreate(['name' => 'Liter'], ['abbreviation' => 'L']);
$unitKali = Unit::firstOrCreate(['name' => 'Kali'], ['abbreviation' => 'Kali']);
$unitKg = Unit::where('name', 'Kg')->orWhere('abbreviation', 'kg')->first();

if (!$unitKg) {
    $unitKg = Unit::create(['name' => 'Kilogram', 'abbreviation' => 'Kg']);
}

// 2. Create Ingredients
$ingredientsData = [
    ['name' => 'Royco', 'unit_id' => $unitBks->id, 'unit_cost' => 18500],
    ['name' => 'Miwon', 'unit_id' => $unitKg->id, 'unit_cost' => 43000],
    ['name' => 'Bawang Putih', 'unit_id' => $unitKg->id, 'unit_cost' => 40000],
    ['name' => 'Jintan', 'unit_id' => $unitKg->id, 'unit_cost' => 74000],
    ['name' => 'Ketumbar', 'unit_id' => $unitKg->id, 'unit_cost' => 22000],
    ['name' => 'Sere', 'unit_id' => $unitIkat->id, 'unit_cost' => 6000],
    ['name' => 'Lengkuas', 'unit_id' => $unitKg->id, 'unit_cost' => 10000],
    ['name' => 'Minyak', 'unit_id' => $unitL->id, 'unit_cost' => 24000],
    ['name' => 'Lombok Besar', 'unit_id' => $unitKg->id, 'unit_cost' => 80000],
    ['name' => 'Giling Bumbu', 'unit_id' => $unitKali->id, 'unit_cost' => 50000],
];

$ingredientIds = [];
foreach ($ingredientsData as $data) {
    $ing = Ingredient::updateOrCreate(
        ['name' => $data['name']],
        [
            'unit_id' => $data['unit_id'], 
            'unit_cost' => $data['unit_cost'],
            'current_stock' => 0, // Inisialisasi stok 0
            'is_active' => true
        ]
    );
    $ingredientIds[$data['name']] = $ing->id;
}

// 3. Setup Recipe for Coto Products
$products = Product::whereIn('name', ['Coto Makassar Daging', 'Coto Makassar Campur'])->get();

$recipeMapping = [
    'Royco' => 138.75 / 18500,
    'Miwon' => 107.50 / 43000,
    'Bawang Putih' => 150 / 40000,
    'Jintan' => 92.50 / 74000,
    'Ketumbar' => 13.75 / 22000,
    'Sere' => 225 / 6000,
    'Lengkuas' => 62.50 / 10000,
    'Minyak' => 240 / 24000,
    'Lombok Besar' => 50 / 80000,
    'Giling Bumbu' => 125 / 50000,
];

foreach ($products as $product) {
    // Optional: Clear existing recipe related to these spices to avoid duplicates if run multiple times
    // We will just clear all for simplicity, or we can carefully delete only these.
    // Let's clear all first.
    RecipeItem::where('product_id', $product->id)->delete();

    foreach ($recipeMapping as $name => $qty) {
        RecipeItem::create([
            'product_id' => $product->id,
            'ingredient_id' => $ingredientIds[$name],
            'quantity' => $qty,
        ]);
    }
}

echo "Berhasil update data resep Coto!\n";
