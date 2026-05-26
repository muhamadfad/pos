<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HistoricalDataSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->first()->id ?? 1;
        $userId = DB::table('users')->first()->id ?? 1;

        // 1. Seed Ingredients & Stock
        $ingredients = [
            ['name' => 'Daging Sapi', 'unit' => 'Kg', 'price' => 120000, 'min' => 10],
            ['name' => 'Kacang Tanah', 'unit' => 'Kg', 'price' => 25000, 'min' => 5],
            ['name' => 'Bawang Merah', 'unit' => 'Kg', 'price' => 30000, 'min' => 5],
            ['name' => 'Bawang Putih', 'unit' => 'Kg', 'price' => 35000, 'min' => 5],
            ['name' => 'Gula Pasir', 'unit' => 'Kg', 'price' => 15000, 'min' => 10],
            ['name' => 'Es Batu', 'unit' => 'Balok', 'price' => 5000, 'min' => 20],
        ];

        foreach ($ingredients as $ing) {
            $unitId = DB::table('units')->where('name', $ing['unit'])->first()->id ?? 
                      DB::table('units')->insertGetId(['name' => $ing['unit'], 'abbreviation' => strtolower($ing['unit'])]);
            
            $ingredientId = DB::table('ingredients')->insertGetId([
                'name' => $ing['name'],
                'unit_id' => $unitId,
                'branch_id' => $branchId,
                'min_stock' => $ing['min'],
                'unit_cost' => $ing['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('branch_stock')->insert([
                'branch_id' => $branchId,
                'ingredient_id' => $ingredientId,
                'quantity' => rand(2, 50),
                'min_stock' => $ing['min'],
                'last_updated' => now(),
            ]);
        }

        // 2. Historical Data for 6 months (Orders & Expenses)
        $products = DB::table('products')->get();
        if ($products->isEmpty()) {
            $this->command->info('Please run PosDummySeeder first.');
            return;
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $daysInMonth = $date->daysInMonth;

            // Generate 10-30 orders per month
            $orderCount = rand(10, 30);
            for ($j = 0; $j < $orderCount; $j++) {
                $orderDate = $date->copy()->day(rand(1, $daysInMonth))->setHour(rand(10, 22))->setMinute(rand(0, 59));
                
                $orderTotal = 0;
                $orderItems = [];
                
                // 1 to 4 items per order
                $itemCount = rand(1, 4);
                $selectedProducts = $products->random($itemCount);
                
                foreach ($selectedProducts as $prod) {
                    $qty = rand(1, 3);
                    $subtotal = $prod->selling_price * $qty;
                    $orderTotal += $subtotal;
                    
                    $orderItems[] = [
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'selling_price' => $prod->selling_price,
                        'cost_price' => $prod->purchase_price,
                        'discount' => 0,
                        'subtotal' => $subtotal,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ];
                }

                $orderId = DB::table('orders')->insertGetId([
                    'client_uuid' => Str::uuid()->toString(),
                    'invoice_number' => 'INV-' . $orderDate->format('ymdHi') . rand(10,99),
                    'customer_id' => null,
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'date' => $orderDate->toDateString(),
                    'source' => 'pos',
                    'subtotal' => $orderTotal,
                    'discount' => 0,
                    'total_amount' => $orderTotal,
                    'total_paid' => $orderTotal,
                    'remaining_receivable' => 0,
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                foreach ($orderItems as &$item) {
                    $item['order_id'] = $orderId;
                }
                DB::table('order_items')->insert($orderItems);
            }

            // Generate 3-8 expenses per month
            $expenseCount = rand(3, 8);
            for ($k = 0; $k < $expenseCount; $k++) {
                $expDate = $date->copy()->day(rand(1, $daysInMonth));
                DB::table('expenses')->insert([
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'category' => collect(['operasional', 'listrik', 'air', 'lainnya'])->random(),
                    'description' => 'Pengeluaran rutin ' . $expDate->format('M Y'),
                    'amount' => rand(50000, 500000),
                    'date' => $expDate->toDateString(),
                    'notes' => 'Pengeluaran bulanan ' . $expDate->format('M Y'),
                    'created_at' => $expDate,
                    'updated_at' => $expDate,
                ]);
            }
        }
        
        $this->command->info('Historical Data Seeded Successfully!');
    }
}
