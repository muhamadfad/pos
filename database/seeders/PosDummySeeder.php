<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Units
        $unitCup = DB::table('units')->insertGetId([
            'name' => 'Cup',
            'abbreviation' => 'cup',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $unitGlass = DB::table('units')->insertGetId([
            'name' => 'Glass',
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
        $catPastry = DB::table('categories')->insertGetId([
            'name' => 'Pastry',
            'description' => 'Makanan ringan dan roti',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catCoffee = DB::table('categories')->insertGetId([
            'name' => 'Coffee',
            'description' => 'Minuman kopi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Products
        DB::table('products')->insert([
            [
                'barcode' => 'P001',
                'name' => 'Espresso',
                'category_id' => $catCoffee,
                'unit_id' => $unitCup,
                'selling_price' => 15000,
                'purchase_price' => 5000,
                'min_stock' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P002',
                'name' => 'Cappuccino',
                'category_id' => $catCoffee,
                'unit_id' => $unitCup,
                'selling_price' => 25000,
                'purchase_price' => 8000,
                'min_stock' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P003',
                'name' => 'Croissant',
                'category_id' => $catPastry,
                'unit_id' => $unitPcs,
                'selling_price' => 20000,
                'purchase_price' => 10000,
                'min_stock' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P004',
                'name' => 'Americano',
                'category_id' => $catCoffee,
                'unit_id' => $unitCup,
                'selling_price' => 18000,
                'purchase_price' => 6000,
                'min_stock' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barcode' => 'P005',
                'name' => 'Caramel Macchiato',
                'category_id' => $catCoffee,
                'unit_id' => $unitCup,
                'selling_price' => 30000,
                'purchase_price' => 12000,
                'min_stock' => 30,
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
