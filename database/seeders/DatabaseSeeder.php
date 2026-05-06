<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya jalankan seeder untuk users dan branches
        $this->call([
            UsersAndBranchesSeeder::class,
        ]);
    }
}
