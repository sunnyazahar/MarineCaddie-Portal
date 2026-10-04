<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_settings') || Schema::hasColumn('company_settings', 'proforma_prefix')) {
            return;
        }

        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('proforma_prefix', 20)->nullable()->after('invoice_notes');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('company_settings', 'proforma_prefix')) {
            Schema::table('company_settings', function (Blueprint $table) {
                $table->dropColumn('proforma_prefix');
            });
        }
    }
};
