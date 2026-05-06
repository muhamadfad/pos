<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersAndBranchesSeeder extends Seeder
{
    public function run(): void
    {
        // ========== SEED BRANCHES ==========
        DB::table('branches')->insert([
            [
                'code' => 'CBG001',
                'name' => 'Cabang Utama',
                'address' => 'Jl. Ahmad Yani No. 123, Makassar',
                'phone' => '081234567890',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CBG002',
                'name' => 'Cabang Panakkukang',
                'address' => 'Jl. Boulevard No. 45, Panakkukang, Makassar',
                'phone' => '081234567891',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CBG003',
                'name' => 'Cabang Sultan Alauddin',
                'address' => 'Jl. Sultan Alauddin No. 78, Makassar',
                'phone' => '081234567892',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ========== SEED USERS ==========
        DB::table('users')->insert([
            [
                'branch_id' => 1,
                'name' => 'Admin Utama',
                'email' => 'superadmin@resto.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => 1,
                'name' => 'Admin Cabang Utama',
                'email' => 'admin@resto.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => 1,
                'name' => 'Kasir 1',
                'email' => 'kasir1@resto.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => 2,
                'name' => 'Admin Cabang Panakkukang',
                'email' => 'admin.panakkukang@resto.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => 2,
                'name' => 'Kasir Panakkukang',
                'email' => 'kasir.panakkukang@resto.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => null,
                'name' => 'Admin Tanpa Cabang',
                'email' => 'admin.no.branch@resto.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}