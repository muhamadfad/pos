<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mitra_id')->constrained('mitras')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('harga_beli', 15, 2);
            $table->date('date');
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};
