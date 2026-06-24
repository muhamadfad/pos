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
            ['name' => 'Daging Sapi', 'unit_id' => $unitKg, 'current_stock' => 10, 'min_stock' => 2, 'unit_cost' => 120000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Jeroan Sapi', 'unit_id' => $unitKg, 'current_stock' => 5, 'min_stock' => 2, 'unit_cost' => 80000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Kacang Tanah', 'unit_id' => $unitKg, 'current_stock' => 15, 'min_stock' => 5, 'unit_cost' => 30000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Bawang Merah', 'unit_id' => $unitKg, 'current_stock' => 5, 'min_stock' => 1, 'unit_cost' => 45000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Bawang Putih', 'unit_id' => $unitKg, 'current_stock' => 4, 'min_stock' => 1, 'unit_cost' => 40000, 'branch_id' => $branchId, 'is_active' => true],
            ['name' => 'Ketupat Instan', 'unit_id' => \Illuminate\Support\Facades\DB::table('units')->where('name', 'Pieces')->value('id') ?? $unitKg, 'current_stock' => 100, 'min_stock' => 20, 'unit_cost' => 1500, 'branch_id' => $branchId, 'is_active' => true],
        ];

        \Illuminate\Support\Facades\DB::table('ingredients')->insert($ingredients);

        // Ambil ID Bahan Baku
        $ingrDaging = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Daging Sapi')->value('id');
        $ingrJeroan = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Jeroan Sapi')->value('id');
        $ingrKacang = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Kacang Tanah')->value('id');
        $ingrKetupat = \Illuminate\Support\Facades\DB::table('ingredients')->where('name', 'Ketupat Instan')->value('id');

        // 3. Hubungkan ke Produk (Resep/Recipe Items)
        // Pastikan product sudah ada dari PosDummySeeder
        $prodCotoDaging = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Coto Makassar Daging')->value('id');
        $prodCotoCampur = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Coto Makassar Campur')->value('id');
        $prodKetupat = \Illuminate\Support\Facades\DB::table('products')->where('name', 'Ketupat / Burasa')->value('id');

        if ($prodCotoDaging) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodCotoDaging, 'ingredient_id' => $ingrDaging, 'quantity' => 0.1, 'unit_id' => $unitKg, 'waste_percentage' => 5, 'created_at' => now(), 'updated_at' => now()], // 100 gram daging
                ['product_id' => $prodCotoDaging, 'ingredient_id' => $ingrKacang, 'quantity' => 0.05, 'unit_id' => $unitKg, 'waste_percentage' => 0, 'created_at' => now(), 'updated_at' => now()], // 50 gram kacang
            ]);
        }

        if ($prodCotoCampur) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodCotoCampur, 'ingredient_id' => $ingrDaging, 'quantity' => 0.05, 'unit_id' => $unitKg, 'waste_percentage' => 5, 'created_at' => now(), 'updated_at' => now()], // 50 gram daging
                ['product_id' => $prodCotoCampur, 'ingredient_id' => $ingrJeroan, 'quantity' => 0.05, 'unit_id' => $unitKg, 'waste_percentage' => 10, 'created_at' => now(), 'updated_at' => now()], // 50 gram jeroan
                ['product_id' => $prodCotoCampur, 'ingredient_id' => $ingrKacang, 'quantity' => 0.05, 'unit_id' => $unitKg, 'waste_percentage' => 0, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($prodKetupat) {
            \Illuminate\Support\Facades\DB::table('recipe_items')->insert([
                ['product_id' => $prodKetupat, 'ingredient_id' => $ingrKetupat, 'quantity' => 1, 'unit_id' => \Illuminate\Support\Facades\DB::table('units')->where('name', 'Pieces')->value('id'), 'waste_percentage' => 0, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
