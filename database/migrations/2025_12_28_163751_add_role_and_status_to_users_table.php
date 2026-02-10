<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_role_and_status_to_users_table.php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'kasir'])->default('kasir');
        $table->boolean('is_active')->default(true);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
