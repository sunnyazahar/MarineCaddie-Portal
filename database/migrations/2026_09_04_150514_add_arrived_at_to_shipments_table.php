<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->timestamp('arrived_at')->nullable()->after('status');
        });

        // Existing Completed rows leave Delivery follow-up (already finished historically).
        DB::table('shipments')
            ->where('status', 'Completed')
            ->whereNull('arrived_at')
            ->update(['arrived_at' => DB::raw('COALESCE(updated_at, NOW())')]);
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('arrived_at');
        });
    }
};
