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
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 6)->change();
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('current_stock', 15, 6)->change();
            $table->decimal('min_stock', 15, 6)->change();
            $table->decimal('unit_cost', 15, 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('current_stock', 15, 2)->change();
            $table->decimal('min_stock', 15, 2)->change();
            $table->decimal('unit_cost', 15, 2)->change();
        });
    }
};
