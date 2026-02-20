<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,    // Users (admin, kasir)
            UnitSeeder::class,     // Satuan (sak, btg, dll)
            SupplierSeeder::class, // Supplier
            CustomerSeeder::class, // Customer
            ProductSeeder::class,  // Kategori + Produk (butuh unit_id)
        ]);
    }
}
