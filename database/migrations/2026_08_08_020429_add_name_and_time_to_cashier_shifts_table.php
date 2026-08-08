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
        Schema::table('cashier_shifts', function (Blueprint $table) {
            $table->string('cashier_name')->nullable()->after('user_id');
            $table->string('shift_time')->nullable()->after('cashier_name'); // e.g. "Pagi (08:00 - 15:00)"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_shifts', function (Blueprint $table) {
            $table->dropColumn(['cashier_name', 'shift_time']);
        });
    }
};
