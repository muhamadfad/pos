<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran_hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')
                  ->constrained('barang_masuk')
                  ->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('metode_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_hutang');
    }
};
