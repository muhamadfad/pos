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
        // Hapus foreign key lama jika ada
        Schema::table('branch_stock', function (Blueprint $table) {
            // Cek dan hapus foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Rename kolom
            $table->renameColumn('product_id', 'ingredient_id');
        });
        
        // Tambahkan foreign key baru ke tabel ingredients
        Schema::table('branch_stock', function (Blueprint $table) {
            $table->foreign('ingredient_id')
                  ->references('id')
                  ->on('ingredients')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_stock', function (Blueprint $table) {
            // Hapus foreign key baru
            $table->dropForeign(['ingredient_id']);
            
            // Kembalikan nama kolom
            $table->renameColumn('ingredient_id', 'product_id');
        });
        
        // Kembalikan foreign key lama
        Schema::table('branch_stock', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }
};