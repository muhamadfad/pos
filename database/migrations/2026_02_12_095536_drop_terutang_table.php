<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('terutang');
    }

    public function down(): void
    {
        Schema::create('terutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')->constrained('barang_masuk')->onDelete('cascade');
            $table->date('jatuh_tempo')->nullable();
            $table->enum('status', ['HUTANG', 'LUNAS'])->default('HUTANG');
            $table->timestamps();
        });
    }
};
