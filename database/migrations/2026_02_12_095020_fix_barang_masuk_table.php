<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {

            if (Schema::hasColumn('barang_masuk', 'transaksi_id')) {
                $table->dropColumn('transaksi_id');
            }

            if (Schema::hasColumn('barang_masuk', 'status')) {
                $table->dropColumn('status');
            }

            if (!Schema::hasColumn('barang_masuk', 'sisa_hutang')) {
                $table->decimal('sisa_hutang', 15, 2)
                      ->default(0)
                      ->after('total_dibayar');
            }

            $table->string('status_pembayaran')
                  ->default('HUTANG')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->unsignedBigInteger('transaksi_id')->nullable();
            $table->dropColumn('sisa_hutang');
        });
    }
};
