<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * orders — transaksi penjualan ke customer
 * Bisa dari POS (kasir) maupun order manual (admin)
 * Header transaksi; detail item ada di order_items
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->nullable();   // no. invoice otomatis
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('cashier_shift_id')->nullable()->constrained('cashier_shifts')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // kasir/admin
            $table->date('date');
            $table->enum('source', ['pos', 'manual'])->default('manual'); // dari mana order dibuat
            $table->decimal('subtotal', 15, 2)->default(0);         // total sebelum diskon
            $table->decimal('discount', 15, 2)->default(0);         // diskon keseluruhan
            $table->decimal('total_amount', 15, 2)->default(0);     // total final (subtotal - discount)
            $table->decimal('total_paid', 15, 2)->default(0);       // total sudah dibayar
            $table->decimal('remaining_receivable', 15, 2)->default(0); // sisa piutang dari customer
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'transfer', 'credit'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
