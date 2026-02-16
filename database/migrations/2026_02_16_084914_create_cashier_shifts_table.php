<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0); // Modal awal
            $table->decimal('expected_cash', 15, 2)->nullable(); // Cash yang seharusnya
            $table->decimal('actual_cash', 15, 2)->nullable(); // Cash yang dihitung
            $table->decimal('total_sales', 15, 2)->default(0); // Total penjualan
            $table->integer('transaction_count')->default(0); // Jumlah transaksi
            $table->text('notes')->nullable(); // Catatan
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
