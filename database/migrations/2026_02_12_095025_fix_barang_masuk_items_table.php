<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barang_masuk_items', function (Blueprint $table) {

            if (Schema::hasColumn('barang_masuk_items', 'harga')) {
                $table->renameColumn('harga', 'harga_beli');
            }

            if (!Schema::hasColumn('barang_masuk_items', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->after('harga_beli');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk_items', function (Blueprint $table) {
            $table->renameColumn('harga_beli', 'harga');
            $table->dropColumn('subtotal');
        });
    }
};
