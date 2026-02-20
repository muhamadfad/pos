<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'    => 'Umum / Walk-in',
                'phone'   => null,
                'address' => null,
                'type'    => 'retail',
                'email'   => null,
                'notes'   => 'Customer umum tanpa akun',
            ],
            [
                'name'    => 'Toko Bangunan Maju',
                'phone'   => '0401-5551101',
                'address' => 'Jl. Pasar No. 5, Kolaka',
                'type'    => 'store',
                'email'   => 'toko.maju@gmail.com',
            ],
            [
                'name'    => 'CV. Bangun Sejahtera',
                'phone'   => '0401-5551102',
                'address' => 'Jl. Industri No. 10, Kolaka',
                'type'    => 'project',
                'email'   => 'bangunsejahtera@gmail.com',
            ],
            [
                'name'    => 'Bapak Hendra Wijaya',
                'phone'   => '081234567891',
                'address' => 'Jl. Merdeka No. 3, Kolaka',
                'type'    => 'retail',
            ],
            [
                'name'    => 'UD. Karya Mandiri',
                'phone'   => '0401-5551103',
                'address' => 'Jl. Raya Kolaka No. 22',
                'type'    => 'store',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['name' => $customer['name']], $customer);
        }
    }
}
