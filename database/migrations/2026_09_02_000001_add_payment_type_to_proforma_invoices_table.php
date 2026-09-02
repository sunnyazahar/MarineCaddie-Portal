<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->string('payment_type', 32)->nullable()->after('einvoice_status');
        });
    }

    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
