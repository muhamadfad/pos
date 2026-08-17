<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-data {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus semua data transaksi dan kembalikan stok menjadi 0 (Kecuali Data Master: Menu, Cabang, User, Supplier, Customer)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('PERINGATAN: Semua data transaksi (Penjualan, Pembelian, Produksi, Distribusi, Kas) akan dihapus secara PERMANEN! Lanjutkan?')) {
                $this->info('Proses reset dibatalkan.');
                return;
            }
        }

        $this->info('Memulai penghapusan data transaksi...');

        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Daftar tabel transaksi yang akan di-truncate (dikosongkan)
        $transactionTables = [
            'orders',
            'order_items',
            'order_payments',
            'cashier_shifts',
            'purchases',
            'purchase_items',
            'purchase_payments',
            'expenses',
            'productions',
            'production_items',
            'stock_transfers',
            'stock_transfer_items',
            'stock_returns',
            'stock_return_items',
            // Jika mau menghapus customer/supplier, tambahkan di sini. 
            // Tapi biasanya master data dibiarkan.
        ];

        foreach ($transactionTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("Tabel {$table} berhasil dikosongkan.");
            }
        }

        // Kembalikan semua stok bahan baku menjadi 0
        $this->info('Mereset stok bahan baku (Inventory) menjadi 0...');
        DB::table('ingredients')->update([
            'current_stock' => 0,
            'unit_cost' => 0
        ]);
        
        // Kosongkan branch_stock jika ada (meskipun sekarang pakai ingredients)
        if (Schema::hasTable('branch_stock')) {
            DB::table('branch_stock')->update(['quantity' => 0]);
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->info('Berhasil! Sistem Anda sekarang bersih dari transaksi (Kembali seperti baru). Data Master (Menu, User, Cabang, Bahan Baku) tetap aman!');
    }
}