<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barang_masuk_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_masuk_id');
            $table->unsignedBigInteger('barang_id');
            $table->integer('qty');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->foreign('barang_masuk_id')->references('id')->on('barang_masuk')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_items');
    }
};
