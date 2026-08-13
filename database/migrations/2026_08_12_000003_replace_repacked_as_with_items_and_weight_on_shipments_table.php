<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('repacked_as');
            $table->unsignedInteger('repacked_items')->nullable()->after('status');
            $table->decimal('repacked_weight', 10, 2)->nullable()->after('repacked_items');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['repacked_items', 'repacked_weight']);
            $table->string('repacked_as', 255)->nullable()->after('status');
        });
    }
};
