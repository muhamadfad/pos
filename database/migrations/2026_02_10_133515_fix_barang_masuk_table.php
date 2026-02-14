<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            if (Schema::hasColumn('barang_masuk', 'product_id')) {
                $table->dropForeign(['product_id']); // hapus FK
                $table->dropColumn('product_id');     // baru hapus kolom
                  $table->dropColumn('quantity');
                Schema::table('barang_masuk', function (Blueprint $table) {
  
});

            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }
};

