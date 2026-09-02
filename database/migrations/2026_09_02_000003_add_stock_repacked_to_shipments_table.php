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
            $table->unsignedInteger('stock_repacked_items')->nullable()->after('repacked_weight');
            $table->decimal('stock_repacked_weight', 10, 2)->nullable()->after('stock_repacked_items');
        });

        DB::table('shipments')
            ->where(function ($query) {
                $query->whereNotNull('repacked_items')
                    ->orWhereNotNull('repacked_weight');
            })
            ->update([
                'stock_repacked_items' => DB::raw('repacked_items'),
                'stock_repacked_weight' => DB::raw('repacked_weight'),
            ]);
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['stock_repacked_items', 'stock_repacked_weight']);
        });
    }
};
