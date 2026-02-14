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
        Schema::table('terutang', function (Blueprint $table) {

            // Tambah kolom barang_masuk_id jika belum ada
            if (!Schema::hasColumn('terutang', 'barang_masuk_id')) {
                $table->unsignedBigInteger('barang_masuk_id')->after('id');

                // Foreign key
                $table->foreign('barang_masuk_id')
                    ->references('id')
                    ->on('barang_masuk')
                    ->onDelete('cascade');
            }

            // Tambah kolom jatuh_tempo jika belum ada
            if (!Schema::hasColumn('terutang', 'jatuh_tempo')) {
                $table->date('jatuh_tempo')->nullable()->after('barang_masuk_id');
            }

            // Tambah kolom sisa_hutang jika belum ada
            if (!Schema::hasColumn('terutang', 'sisa_hutang')) {
                $table->decimal('sisa_hutang', 15, 2)->default(0)->after('jatuh_tempo');
            }

            // Tambah kolom status jika belum ada
            if (!Schema::hasColumn('terutang', 'status')) {
                $table->enum('status', ['belum_lunas','lunas'])
                    ->default('belum_lunas')
                    ->after('sisa_hutang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terutang', function (Blueprint $table) {
            if (Schema::hasColumn('terutang', 'barang_masuk_id')) {
                $table->dropForeign(['barang_masuk_id']);
                $table->dropColumn('barang_masuk_id');
            }
            if (Schema::hasColumn('terutang', 'jatuh_tempo')) {
                $table->dropColumn('jatuh_tempo');
            }
            if (Schema::hasColumn('terutang', 'sisa_hutang')) {
                $table->dropColumn('sisa_hutang');
            }
            if (Schema::hasColumn('terutang', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
