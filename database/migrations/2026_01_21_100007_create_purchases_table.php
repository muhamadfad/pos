<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * purchases — transaksi pembelian barang dari supplier
 * Header transaksi; detail item ada di purchase_items
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();         // no. faktur / PO
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->date('date');
            $table->decimal('total_amount', 15, 2)->default(0);    // total nilai pembelian
            $table->decimal('total_paid', 15, 2)->default(0);      // total sudah dibayar
            $table->decimal('remaining_debt', 15, 2)->default(0);  // sisa hutang ke supplier
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'transfer', 'credit'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
