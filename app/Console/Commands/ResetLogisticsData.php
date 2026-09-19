<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetLogisticsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-logistics-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus data produksi, inventory, pengembalian, dan pembelian supplier sesuai permintaan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('Menghapus data produksi, inventory, pengembalian, dan pembelian supplier...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Produksi
        DB::table('production_items')->truncate();
        DB::table('productions')->truncate();
        $this->info('Data produksi dihapus.');

        // 2. Inventory / Stock
        DB::table('branch_stocks')->truncate();
        DB::table('stock_transfer_items')->truncate();
        DB::table('stock_transfers')->truncate();
        $this->info('Data inventory dan transfer/stock dihapus.');

        // 3. Pengembalian
        DB::table('daily_return_summaries')->truncate();
        $this->info('Data pengembalian (return) dihapus.');

        // 4. Pembelian Supplier
        DB::table('purchase_items')->truncate();
        DB::table('purchase_payments')->truncate();
        DB::table('purchases')->truncate();
        $this->info('Data pembelian supplier dihapus.');

        // Hapus pengeluaran (expenses) yang terbuat otomatis dari Pembelian
        DB::table('expenses')->where('category', 'Pembelian Bahan Baku')->delete();
        $this->info('Data pengeluaran dari pembelian bahan baku dihapus.');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('SELESAI! Data penjualan, menu, user, dan cabang TETAP AMAN.');
    }
}
