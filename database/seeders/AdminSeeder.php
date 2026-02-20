<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@depobangun.com'],
            [
                'name'      => 'Administrator',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Kasir
        User::firstOrCreate(
            ['email' => 'kasir@depobangun.com'],
            [
                'name'      => 'Kasir',
                'password'  => Hash::make('password'),
                'role'      => 'kasir',
                'is_active' => true,
            ]
        );
    }
}
