<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name'    => 'PT. Semen Tiga Roda',
                'phone'   => '021-5551001',
                'email'   => 'semen3roda@supplier.com',
                'address' => 'Jl. Industri No. 1, Jakarta',
            ],
            [
                'name'    => 'CV. Besi Jaya',
                'phone'   => '0411-5552002',
                'email'   => 'besijaya@supplier.com',
                'address' => 'Jl. Raya Industri No. 45, Makassar',
            ],
            [
                'name'    => 'UD. Cat Warna Indah',
                'phone'   => '0411-5553003',
                'email'   => 'catwarna@supplier.com',
                'address' => 'Jl. Pasar Baru No. 12, Makassar',
            ],
            [
                'name'    => 'PT. Wavin Indonesia',
                'phone'   => '021-5554004',
                'email'   => 'wavin@supplier.com',
                'address' => 'Jl. Rungkut Industri No. 7, Surabaya',
            ],
            [
                'name'    => 'CV. Keramik Mulia',
                'phone'   => '0401-5555005',
                'email'   => 'keramikmulia@supplier.com',
                'address' => 'Jl. Kolaka Raya No. 88, Kolaka',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
