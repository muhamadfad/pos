<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Units
        $unitPorsi = DB::table('units')->insertGetId([
            'name' => 'Porsi',
            'abbreviation' => 'prs',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $unitGelas = DB::table('units')->insertGetId([
            'name' => 'Gelas',
            'abbreviation' => 'gls',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $unitPcs = DB::table('units')->insertGetId([
            'name' => 'Pieces',
            'abbreviation' => 'pcs',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Categories
        $catMakanan = DB::table('categories')->insertGetId([
            'name' => 'Makanan',
            'description' => 'Menu utama dan pendamping',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catMinuman = DB::table('categories')->insertGetId([
            'name' => 'Minuman',
            'description' => 'Minuman dingin dan panas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Products
        DB::table('products')->insert([
            [
                'barcode' => 'P001',
                'name' => 'Coto Makassar Daging',
                'category_id' => $catMakanan,
                'unit_id' => $unitPorsi,
                'selling_price' => 35000,
                'purchase_price' => 20000,
                'min_stock' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P002',
                'name' => 'Coto Makassar Campur',
                'category_id' => $catMakanan,
                'unit_id' => $unitPorsi,
                'selling_price' => 30000,
                'purchase_price' => 18000,
                'min_stock' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P003',
                'name' => 'Ketupat / Burasa',
                'category_id' => $catMakanan,
                'unit_id' => $unitPcs,
                'selling_price' => 3000,
                'purchase_price' => 1500,
                'min_stock' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P004',
                'name' => 'Es Teh Manis',
                'category_id' => $catMinuman,
                'unit_id' => $unitGelas,
                'selling_price' => 5000,
                'purchase_price' => 2000,
                'min_stock' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P005',
                'name' => 'Es Jeruk',
                'category_id' => $catMinuman,
                'unit_id' => $unitGelas,
                'selling_price' => 10000,
                'purchase_price' => 4000,
                'min_stock' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Seed Customers
        DB::table('customers')->insert([
            [
                'name' => 'Bapak Budi',
                'phone' => '081234567800',
                'address' => 'Jl. Mawar No 1',
                'type' => 'retail',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ibu Siti',
                'phone' => '081234567811',
                'address' => 'Jl. Melati No 2',
                'type' => 'retail',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toko Maju Jaya',
                'phone' => '081234567822',
                'address' => 'Jl. Kamboja No 3',
                'type' => 'store',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
