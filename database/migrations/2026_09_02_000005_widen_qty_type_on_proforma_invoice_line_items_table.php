<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_invoice_line_items', function (Blueprint $table) {
            $table->string('qty_type', 32)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('proforma_invoice_line_items', function (Blueprint $table) {
            $table->string('qty_type', 8)->nullable()->change();
        });
    }
};
