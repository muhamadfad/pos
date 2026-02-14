<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            
            $table->decimal('total_harga', 15, 2)->after('tanggal');
            $table->decimal('total_dibayar', 15, 2)->after('total_harga')->default(0);
            $table->enum('status_pembayaran', ['lunas', 'terutang'])->after('total_dibayar')->default('lunas');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'total_harga', 'total_dibayar', 'status_pembayaran']);
        });
    }
};
