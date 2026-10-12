<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientAndRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan Unit yang ada (berasumsi Kg dan Gram sudah ada, jika belum buatkan)
        $unitKg = \Illuminate\Support\Facades\DB::table('units')->where('name', 'Kg')->value('id');
        if (!$unitKg) {
            $unitKg = \Illuminate\Support\Facades\DB::table('units')->insertGetId(['name' => 'Kg', 'abbreviation' => 'kg']);
        }

        $unitGram = \Illuminate\Support\Facades\DB::table('units')->where('name', 'Gram')->value('id');
        if (!$unitGram) {
            $unitGram = \Illuminate\Support\Facades\DB::table('units')->insertGetId(['name' => 'Gram', 'abbreviation' => 'gr']);
        }

        $unitLiter = \Illuminate\Support\Facades\DB::table('units')->where('name', 'Liter')->value('id');
        if (!$unitLiter) {
            $unitLiter = \Illuminate\Support\Facades\DB::table('units')->insertGetId(['name' => 'Liter', 'abbreviation' => 'L']);
        }

        $branchId = \Illuminate\Support\Facades\DB::table('branches')->first()->id ?? null;

        // 2. Buat Bahan Baku (Ingredients)
        $ingredients = [
            ['name' => 'Biji Kopi Arabica', 'unit_id' => $unitKg, 'current_stock' => 10, 'min_stock' => 2, 'unit_cost' => 150000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Biji Kopi Robusta', 'unit_id' => $unitKg, 'current_stock' => 5, 'min_stock' => 2, 'unit_cost' => 100000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Susu Segar (Fresh Milk)', 'unit_id' => $unitLiter, 'current_stock' => 20, 'min_stock' => 5, 'unit_cost' => 20000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Sirup Karamel', 'unit_id' => $unitLiter, 'current_stock' => 5, 'min_stock' => 1, 'unit_cost' => 80000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Sirup Gula', 'unit_id' => $unitLiter, 'current_stock' => 4, 'min_stock' => 1, 'unit_cost' => 40000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Frozen Croissant', 'unit_id' => \Illuminate\Support\Facades\DB::table('units')->where('name', 'Pieces')->value('id') ?? $unitKg, 'current_stock' => 100, 'min_stock' => 20, 'unit_cost' => 8000, 'branch_id' => $branchId, 'is_active' => true],
        ];

        \Illuminate\Support\Facades\DB::table('ingredients')->insert($ingredients);

        // Ambil ID Bahan Baku
        $ingrArabica = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Biji Kopi Arabica')->value('id');
        $ingrRobusta = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Biji Kopi Robusta')->value('id');
        $ingrMilk = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Susu Segar (Fresh Milk)')->value('id');
        $ingrCaramel = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Sirup Karamel')->value('id');
        $ingrCroissant = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Frozen Croissant')->value('id');

        // 3. Hubungkan ke Produk (Resep/Recipe Items)
        // Pastikan product sudah ada dari PosDummySeeder
        $prodEspresso = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Espresso')->value('id');
        $prodCappuccino = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Cappuccino')->value('id');
        $prodCroissant = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Croissant')->value('id');
        $prodCaramelMacchiato = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Caramel Macchiato')->value('id');

        if ($prodEspresso) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodEspresso, 'ingredient_id' => $ingrArabica, 'quantity' => 0.015, 'unit_id' => $unitKg, 'waste_percentage' => 5, 'created_at' => now(), 'updated_at' => now()], // 15 gram kopi
            ]);
        }

        if ($prodCappuccino) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodCappuccino, 'ingredient_id' => $ingrArabica, 'quantity' => 0.015, 'unit_id' => $unitKg, 'waste_percentage' => 5, 'created_at' => now(), 'updated_at' => now()], // 15 gram kopi
                ['product_id' => $prodCappuccino, 'ingredient_id' => $ingrMilk, 'quantity' => 0.15, 'unit_id' => $unitLiter, 'waste_percentage' => 2, 'created_at' => now(), 'updated_at' => now()], // 150 ml susu
            ]);
        }

        if ($prodCaramelMacchiato) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodCaramelMacchiato, 'ingredient_id' => $ingrArabica, 'quantity' => 0.015, 'unit_id' => $unitKg, 'waste_percentage' => 5, 'created_at' => now(), 'updated_at' => now()], // 15 gram kopi
                ['product_id' => $prodCaramelMacchiato, 'ingredient_id' => $ingrMilk, 'quantity' => 0.15, 'unit_id' => $unitLiter, 'waste_percentage' => 2, 'created_at' => now(), 'updated_at' => now()], // 150 ml susu
                ['product_id' => $prodCaramelMacchiato, 'ingredient_id' => $ingrCaramel, 'quantity' => 0.02, 'unit_id' => $unitLiter, 'waste_percentage' => 2, 'created_at' => now(), 'updated_at' => now()], // 20 ml sirup
            ]);
        }

        if ($prodCroissant) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodCroissant, 'ingredient_id' => $ingrCroissant, 'quantity' => 1, 'unit_id' => \Illuminate\Support\Facades\DB::table('units')->where('name', 'Pieces')->value('id'), 'waste_percentage' => 0, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
