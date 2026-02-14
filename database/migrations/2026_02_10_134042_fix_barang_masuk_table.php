<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            if (Schema::hasColumn('barang_masuk', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->integer('quantity')->default(0);
        });
    }
};
