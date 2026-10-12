<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackupSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks before seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('branches')->insert(array (
  0 => 
  array (
    'id' => 1,
    'code' => 'CBG001',
    'name' => 'Cabang Utama',
    'address' => 'Jl. Kopi No. 1',
    'phone' => '081234567890',
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:45',
    'updated_at' => '2026-06-14 20:15:42',
  ),
));

        DB::table('users')->insert(array (
  0 => 
  array (
    'id' => 1,
    'branch_id' => 1,
    'name' => 'Admin Utama',
    'email' => 'superadmin@kopi.com',
    'email_verified_at' => '2026-05-17 07:58:45',
    'password' => '$2y$12$OTVnWz5Kek15Yu9kOqx1iuZxwmb2BOI0rPf7aS8iGv6wnRo1AybAK',
    'role' => 'super_admin',
    'can_manage_pusat' => 0,
    'is_decoy' => 0,
    'is_active' => 1,
    'remember_token' => NULL,
    'created_at' => '2026-05-17 07:58:46',
    'updated_at' => '2026-09-26 22:18:59',
  ),
  1 => 
  array (
    'id' => 2,
    'branch_id' => 1,
    'name' => 'Admin Cabang Utama',
    'email' => 'admincabangsaosao@kopi.com',
    'email_verified_at' => '2026-05-17 07:58:46',
    'password' => '$2y$12$2Vl1kyUIdKwZisZtZ2bBj.sGUaqrOGvYun3ywbFneV0JoiReFyYz.',
    'role' => 'admin',
    'can_manage_pusat' => 0,
    'is_decoy' => 0,
    'is_active' => 1,
    'remember_token' => NULL,
    'created_at' => '2026-05-17 07:58:46',
    'updated_at' => '2026-06-14 20:15:42',
  ),
  2 => 
  array (
    'id' => 3,
    'branch_id' => 1,
    'name' => 'Kasir Cabang Utama',
    'email' => 'kasircabangsaosao@kopi.com',
    'email_verified_at' => '2026-05-17 07:58:46',
    'password' => '$2y$12$pgcrCfYda8emBMWxn0QMge.qpK3znE05SFhLCvaYmLsxr6MklGejO',
    'role' => 'kasir',
    'can_manage_pusat' => 0,
    'is_decoy' => 0,
    'is_active' => 1,
    'remember_token' => NULL,
    'created_at' => '2026-05-17 07:58:46',
    'updated_at' => '2026-06-14 20:15:42',
  ),
  3 => 
  array (
    'id' => 6,
    'branch_id' => NULL,
    'name' => 'Admin Tanpa Cabang',
    'email' => 'admin.no.branch@kopi.com',
    'email_verified_at' => '2026-05-17 07:58:47',
    'password' => '$2y$12$QmCLMNyjxDVUjZZA3trjfON6FI7n6zddOfOKCrgFakwcW2Gh25qZy',
    'role' => 'admin',
    'can_manage_pusat' => 0,
    'is_decoy' => 0,
    'is_active' => 1,
    'remember_token' => NULL,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
  ),
  4 => 
  array (
    'id' => 7,
    'branch_id' => NULL,
    'name' => 'Owner Report',
    'email' => 'owner_report@kopi.com',
    'email_verified_at' => NULL,
    'password' => '$2y$12$43oA5f13WJ5nASbAt8ttveUQ0sKES9PqEc1d5UhsCoSwsbmdnjm/i',
    'role' => 'super_admin',
    'can_manage_pusat' => 0,
    'is_decoy' => 1,
    'is_active' => 1,
    'remember_token' => NULL,
    'created_at' => '2026-06-14 20:06:07',
    'updated_at' => '2026-06-14 20:19:32',
  ),
  5 => 
  array (
    'id' => 8,
    'branch_id' => 1,
    'name' => 'Cabang Sao Sao Report',
    'email' => 'cabangsaosao_report@kopi.com',
    'email_verified_at' => NULL,
    'password' => '$2y$12$91YuW.nWjckrUsaigCZB6.gxqZBER2apWxfWjYO8sgnc/9CyOxcCa',
    'role' => 'admin',
    'can_manage_pusat' => 0,
    'is_decoy' => 1,
    'is_active' => 1,
    'remember_token' => NULL,
    'created_at' => '2026-06-14 20:06:08',
    'updated_at' => '2026-06-14 20:19:32',
  ),
));

        DB::table('units')->insert(array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Porsi',
    'abbreviation' => 'prs',
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Gelas',
    'abbreviation' => 'gls',
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'Pieces',
    'abbreviation' => 'pcs',
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'Kg',
    'abbreviation' => 'kg',
    'created_at' => NULL,
    'updated_at' => NULL,
    'deleted_at' => NULL,
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'Balok',
    'abbreviation' => 'balok',
    'created_at' => NULL,
    'updated_at' => NULL,
    'deleted_at' => NULL,
  ),
  5 => 
  array (
    'id' => 6,
    'name' => 'Bungkus',
    'abbreviation' => 'Bks',
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:05:04',
    'deleted_at' => NULL,
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'Ikat',
    'abbreviation' => 'Ikat',
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:05:04',
    'deleted_at' => NULL,
  ),
  7 => 
  array (
    'id' => 8,
    'name' => 'Liter',
    'abbreviation' => 'L',
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:05:04',
    'deleted_at' => NULL,
  ),
  8 => 
  array (
    'id' => 9,
    'name' => 'Kali',
    'abbreviation' => 'Kali',
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:05:04',
    'deleted_at' => NULL,
  ),
));

        DB::table('categories')->insert(array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Makanan',
    'description' => 'Menu utama dan pendamping',
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Minuman',
    'description' => 'Minuman dingin dan panas',
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
));

        DB::table('customers')->insert(array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Bapak Budi',
    'phone' => '081234567800',
    'address' => 'Jl. Mawar No 1',
    'type' => 'retail',
    'email' => NULL,
    'notes' => NULL,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Ibu Siti',
    'phone' => '081234567811',
    'address' => 'Jl. Melati No 2',
    'type' => 'retail',
    'email' => NULL,
    'notes' => NULL,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'Toko Maju Jaya',
    'phone' => '081234567822',
    'address' => 'Jl. Kamboja No 3',
    'type' => 'store',
    'email' => NULL,
    'notes' => NULL,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 07:58:47',
    'deleted_at' => NULL,
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'Walk-in Customer',
    'phone' => NULL,
    'address' => NULL,
    'type' => 'retail',
    'email' => NULL,
    'notes' => NULL,
    'is_active' => 1,
    'created_at' => '2026-06-14 18:46:14',
    'updated_at' => '2026-06-14 18:46:14',
    'deleted_at' => NULL,
  ),
));

        DB::table('products')->insert(array (
  0 => 
  array (
    'id' => 1,
    'barcode' => 'P001',
    'name' => 'Espresso',
    'image' => 'products/8mRm9JdRZIXcY4A9NA9KtWIgfo5xKTXKtE0VUBbo.webp',
    'category_id' => 1,
    'unit_id' => 1,
    'selling_price' => '20000.00',
    'purchase_price' => '20000.00',
    'min_stock' => 10,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-06-14 19:20:10',
    'deleted_at' => NULL,
  ),
  1 => 
  array (
    'id' => 2,
    'barcode' => 'P002',
    'name' => 'Cappuccino',
    'image' => 'products/EksVtuvUERr8qR1m1CnqXKZ7hqBMDktzEVqpSaAH.webp',
    'category_id' => 1,
    'unit_id' => 1,
    'selling_price' => '20000.00',
    'purchase_price' => '18000.00',
    'min_stock' => 10,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-06-14 19:20:00',
    'deleted_at' => NULL,
  ),
  2 => 
  array (
    'id' => 3,
    'barcode' => 'P003',
    'name' => 'Ketupat / Burasa',
    'image' => 'products/odQoRIbtzA05HLZeDpC6vnrX0xuIvCtq7mALjLoi.jpg',
    'category_id' => 1,
    'unit_id' => 3,
    'selling_price' => '3000.00',
    'purchase_price' => '1500.00',
    'min_stock' => 50,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 08:47:48',
    'deleted_at' => NULL,
  ),
  3 => 
  array (
    'id' => 4,
    'barcode' => 'P004',
    'name' => 'Es Teh Manis',
    'image' => 'products/jaIPfh9GlDkRIInpqrl8PqVnZXVirMPsgYCJ7KQU.jpg',
    'category_id' => 2,
    'unit_id' => 2,
    'selling_price' => '5000.00',
    'purchase_price' => '2000.00',
    'min_stock' => 20,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 08:47:38',
    'deleted_at' => NULL,
  ),
  4 => 
  array (
    'id' => 5,
    'barcode' => 'P005',
    'name' => 'Es Jeruk',
    'image' => 'products/7Pvcp2DhXlDjazvLK3BbP7rVkNKbdC9ACLEsd4sr.jpg',
    'category_id' => 2,
    'unit_id' => 2,
    'selling_price' => '10000.00',
    'purchase_price' => '4000.00',
    'min_stock' => 20,
    'is_active' => 1,
    'created_at' => '2026-05-17 07:58:47',
    'updated_at' => '2026-05-17 08:47:30',
    'deleted_at' => NULL,
  ),
));

        DB::table('ingredients')->insert(array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Biji Kopi Arabica',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '10.000000',
    'unit_cost' => '120000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:18:26',
    'updated_at' => '2026-05-17 10:18:26',
    'deleted_at' => NULL,
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Biji Kopi Arabica',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '10.000000',
    'unit_cost' => '120000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:19:09',
    'updated_at' => '2026-05-17 10:19:09',
    'deleted_at' => NULL,
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'Susu Segar',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '5.000000',
    'unit_cost' => '25000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:19:09',
    'updated_at' => '2026-05-17 10:19:09',
    'deleted_at' => NULL,
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'Gula Aren',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '5.000000',
    'unit_cost' => '30000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:19:09',
    'updated_at' => '2026-05-17 10:19:09',
    'deleted_at' => NULL,
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'Sirup Karamel',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '5.000000',
    'unit_cost' => '40000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:19:09',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  5 => 
  array (
    'id' => 6,
    'name' => 'Gula Pasir',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '10.000000',
    'unit_cost' => '15000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:19:09',
    'updated_at' => '2026-05-17 10:19:09',
    'deleted_at' => NULL,
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'Es Batu',
    'unit_id' => 5,
    'current_stock' => '0.000000',
    'min_stock' => '20.000000',
    'unit_cost' => '5000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:19:09',
    'updated_at' => '2026-05-17 10:19:09',
    'deleted_at' => NULL,
  ),
  7 => 
  array (
    'id' => 8,
    'name' => 'Biji Kopi Arabica',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '10.000000',
    'unit_cost' => '120000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:25:29',
    'updated_at' => '2026-05-17 10:25:29',
    'deleted_at' => NULL,
  ),
  8 => 
  array (
    'id' => 9,
    'name' => 'Susu Segar',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '5.000000',
    'unit_cost' => '25000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:25:29',
    'updated_at' => '2026-05-17 10:25:29',
    'deleted_at' => NULL,
  ),
  9 => 
  array (
    'id' => 10,
    'name' => 'Gula Aren',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '5.000000',
    'unit_cost' => '30000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:25:29',
    'updated_at' => '2026-05-17 10:25:29',
    'deleted_at' => NULL,
  ),
  10 => 
  array (
    'id' => 11,
    'name' => 'Sirup Karamel',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '5.000000',
    'unit_cost' => '35000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:25:29',
    'updated_at' => '2026-05-17 10:25:29',
    'deleted_at' => NULL,
  ),
  11 => 
  array (
    'id' => 12,
    'name' => 'Gula Pasir',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '10.000000',
    'unit_cost' => '15000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:25:29',
    'updated_at' => '2026-05-17 10:25:29',
    'deleted_at' => NULL,
  ),
  12 => 
  array (
    'id' => 13,
    'name' => 'Es Batu',
    'unit_id' => 5,
    'current_stock' => '0.000000',
    'min_stock' => '20.000000',
    'unit_cost' => '5000.000000',
    'is_active' => 1,
    'branch_id' => 1,
    'created_at' => '2026-05-17 10:25:29',
    'updated_at' => '2026-05-17 10:25:29',
    'deleted_at' => NULL,
  ),
  13 => 
  array (
    'id' => 14,
    'name' => 'Biji Kopi Robusta',
    'unit_id' => 6,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '18500.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  14 => 
  array (
    'id' => 15,
    'name' => 'Sirup Vanilla',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '43000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  15 => 
  array (
    'id' => 16,
    'name' => 'Susu Kental Manis',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '74000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  16 => 
  array (
    'id' => 17,
    'name' => 'Cokelat Bubuk',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '22000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  17 => 
  array (
    'id' => 18,
    'name' => 'Matcha Powder',
    'unit_id' => 7,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '6000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  18 => 
  array (
    'id' => 19,
    'name' => 'Krimmer',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '10000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  19 => 
  array (
    'id' => 20,
    'name' => 'Whipped Cream',
    'unit_id' => 8,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '24000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  20 => 
  array (
    'id' => 21,
    'name' => 'Oat Milk',
    'unit_id' => 4,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '80000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
  21 => 
  array (
    'id' => 22,
    'name' => 'Sirup Hazelnut',
    'unit_id' => 9,
    'current_stock' => '0.000000',
    'min_stock' => '0.000000',
    'unit_cost' => '50000.000000',
    'is_active' => 1,
    'branch_id' => NULL,
    'created_at' => '2026-06-14 19:05:04',
    'updated_at' => '2026-06-14 19:11:30',
    'deleted_at' => NULL,
  ),
));

        DB::table('recipe_items')->insert(array (
  0 => 
  array (
    'id' => 41,
    'product_id' => 1,
    'ingredient_id' => 14,
    'quantity' => '0.007500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  1 => 
  array (
    'id' => 42,
    'product_id' => 1,
    'ingredient_id' => 15,
    'quantity' => '0.002500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  2 => 
  array (
    'id' => 43,
    'product_id' => 1,
    'ingredient_id' => 5,
    'quantity' => '0.003750',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  3 => 
  array (
    'id' => 44,
    'product_id' => 1,
    'ingredient_id' => 16,
    'quantity' => '0.001250',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  4 => 
  array (
    'id' => 45,
    'product_id' => 1,
    'ingredient_id' => 17,
    'quantity' => '0.000625',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  5 => 
  array (
    'id' => 46,
    'product_id' => 1,
    'ingredient_id' => 18,
    'quantity' => '0.037500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  6 => 
  array (
    'id' => 47,
    'product_id' => 1,
    'ingredient_id' => 19,
    'quantity' => '0.006250',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  7 => 
  array (
    'id' => 48,
    'product_id' => 1,
    'ingredient_id' => 20,
    'quantity' => '0.010000',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  8 => 
  array (
    'id' => 49,
    'product_id' => 1,
    'ingredient_id' => 21,
    'quantity' => '0.000625',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  9 => 
  array (
    'id' => 50,
    'product_id' => 1,
    'ingredient_id' => 22,
    'quantity' => '0.002500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  10 => 
  array (
    'id' => 51,
    'product_id' => 2,
    'ingredient_id' => 14,
    'quantity' => '0.007500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  11 => 
  array (
    'id' => 52,
    'product_id' => 2,
    'ingredient_id' => 15,
    'quantity' => '0.002500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  12 => 
  array (
    'id' => 53,
    'product_id' => 2,
    'ingredient_id' => 5,
    'quantity' => '0.003750',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  13 => 
  array (
    'id' => 54,
    'product_id' => 2,
    'ingredient_id' => 16,
    'quantity' => '0.001250',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  14 => 
  array (
    'id' => 55,
    'product_id' => 2,
    'ingredient_id' => 17,
    'quantity' => '0.000625',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  15 => 
  array (
    'id' => 56,
    'product_id' => 2,
    'ingredient_id' => 18,
    'quantity' => '0.037500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  16 => 
  array (
    'id' => 57,
    'product_id' => 2,
    'ingredient_id' => 19,
    'quantity' => '0.006250',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  17 => 
  array (
    'id' => 58,
    'product_id' => 2,
    'ingredient_id' => 20,
    'quantity' => '0.010000',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  18 => 
  array (
    'id' => 59,
    'product_id' => 2,
    'ingredient_id' => 21,
    'quantity' => '0.000625',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
  19 => 
  array (
    'id' => 60,
    'product_id' => 2,
    'ingredient_id' => 22,
    'quantity' => '0.002500',
    'unit_id' => NULL,
    'created_at' => '2026-06-14 19:16:42',
    'updated_at' => '2026-06-14 19:16:42',
  ),
));

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
