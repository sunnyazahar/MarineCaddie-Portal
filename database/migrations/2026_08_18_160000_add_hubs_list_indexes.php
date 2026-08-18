<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->index('hub_name', 'hubs_hub_name_idx');
            $table->index('country', 'hubs_country_idx');
            $table->index('hide_in_portal', 'hubs_hide_in_portal_idx');
        });
    }

    public function down(): void
    {
        Schema::table('hubs', function (Blueprint $table) {
            $table->dropIndex('hubs_hub_name_idx');
            $table->dropIndex('hubs_country_idx');
            $table->dropIndex('hubs_hide_in_portal_idx');
        });
    }
};
