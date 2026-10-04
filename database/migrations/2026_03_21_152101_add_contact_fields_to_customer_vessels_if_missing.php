<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BOOLEAN_COLUMNS = [
        'contact_stocklists',
        'contact_pre_alerts',
        'contact_stock_notifications',
        'contact_free_storage_notifications',
        'contact_offers',
    ];

    public function up(): void
    {
        Schema::table('customer_vessels', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_vessels', 'contact_id')) {
                $table->unsignedBigInteger('contact_id')->after('customer_id')->nullable();
            }

            foreach (self::BOOLEAN_COLUMNS as $column) {
                if (! Schema::hasColumn('customer_vessels', $column)) {
                    $table->boolean($column)->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        // Columns are owned by 2026_03_21_100227 on existing installs.
    }
};
