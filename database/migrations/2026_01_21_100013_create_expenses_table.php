<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * expenses — pengeluaran operasional (bukan pembelian stok)
 * e.g. listrik, gaji, sewa, dll
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // siapa yang input
            $table->string('category')->nullable();     // e.g. operational, salary, utility, other
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('reference')->nullable();    // no. kwitansi / bukti
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
