<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;                 // ← WAJIB
use Illuminate\Support\Facades\Hash; // ← WAJIB

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
