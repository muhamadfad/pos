<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pusat = Branch::where('code', 'PUSAT')->first();
        if ($pusat) {
            DB::table('purchases')
                ->whereNull('branch_id')
                ->update(['branch_id' => $pusat->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible because we don't know which ones were previously null
    }
};
