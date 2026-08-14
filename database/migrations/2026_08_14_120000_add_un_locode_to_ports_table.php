<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            if (! Schema::hasColumn('ports', 'un_locode')) {
                $table->string('un_locode', 8)->nullable()->after('iata_code')->comment('UN/LOCODE (seaports)');
            }
        });

        try {
            Schema::table('ports', function (Blueprint $table) {
                $table->unique(['type', 'un_locode'], 'ports_type_un_locode_unique');
            });
        } catch (\Throwable) {
            // index may already exist
        }
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            try {
                $table->dropUnique('ports_type_un_locode_unique');
            } catch (\Throwable) {
                // ignore
            }

            if (Schema::hasColumn('ports', 'un_locode')) {
                $table->dropColumn('un_locode');
            }
        });
    }
};
