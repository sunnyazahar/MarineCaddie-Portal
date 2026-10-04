<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ports', 'un_locode')) {
            return;
        }

        // Fresh installs get varchar(5) from create_ports_table; codes like INNSA1 need 8.
        Schema::table('ports', function (Blueprint $table) {
            $table->string('un_locode', 8)->nullable()->comment('UN/LOCODE (seaports)')->change();
        });
    }

    public function down(): void
    {
        // Narrowing back to 5 would truncate existing codes.
    }
};
