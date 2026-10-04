<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('proforma_invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 14, 2)->nullable()->after('payment_type');
            }
            if (! Schema::hasColumn('proforma_invoices', 'due_amount')) {
                $table->decimal('due_amount', 14, 2)->nullable()->after('paid_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'due_amount']);
        });
    }
};
