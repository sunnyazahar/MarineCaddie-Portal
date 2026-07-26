<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            if (! Schema::hasColumn('ports', 'icao_code')) {
                $table->string('icao_code', 4)->nullable()->after('iata_code')->comment('ICAO code (airports)');
            }
            if (! Schema::hasColumn('ports', 'un_locode')) {
                $table->string('un_locode', 5)->nullable()->after('icao_code')->comment('UN/LOCODE (seaports)');
            }
            if (! Schema::hasColumn('ports', 'port_name')) {
                $table->string('port_name')->nullable()->after('un_locode');
            }
            if (! Schema::hasColumn('ports', 'country_name')) {
                $table->string('country_name')->nullable()->after('city');
            }
            if (! Schema::hasColumn('ports', 'country_code')) {
                $table->string('country_code', 2)->nullable()->after('country_name');
            }
            if (! Schema::hasColumn('ports', 'flag')) {
                $table->string('flag', 8)->nullable()->after('country_code');
            }
            if (! Schema::hasColumn('ports', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('country_id');
            }
            if (! Schema::hasColumn('ports', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });

        try {
            Schema::table('ports', function (Blueprint $table) {
                $table->unique(['type', 'un_locode'], 'ports_type_un_locode_unique');
            });
        } catch (\Throwable) {
            // index may already exist
        }

        try {
            Schema::table('ports', function (Blueprint $table) {
                $table->index('port_name', 'ports_port_name_index');
            });
        } catch (\Throwable) {
            // index may already exist
        }
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            foreach (['ports_type_un_locode_unique', 'ports_port_name_index'] as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Throwable) {
                    // ignore
                }
            }
        });

        $cols = collect([
            'icao_code',
            'un_locode',
            'port_name',
            'country_name',
            'country_code',
            'flag',
            'latitude',
            'longitude',
        ])->filter(fn (string $col) => Schema::hasColumn('ports', $col))->values()->all();

        if ($cols !== []) {
            Schema::table('ports', function (Blueprint $table) use ($cols) {
                $table->dropColumn($cols);
            });
        }
    }
};
