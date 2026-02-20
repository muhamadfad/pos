<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);  // modal awal kas
            $table->decimal('total_sales', 15, 2)->default(0);      // total penjualan shift ini
            $table->integer('transaction_count')->default(0);        // jumlah transaksi
            $table->decimal('expected_cash', 15, 2)->nullable();     // kas yang seharusnya ada
            $table->decimal('actual_cash', 15, 2)->nullable();       // kas yang dihitung saat tutup
            $table->decimal('cash_difference', 15, 2)->nullable();   // selisih (actual - expected)
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
