<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('proforma_no')->unique();
            $table->string('financial_year_label', 8);
            $table->unsignedInteger('sequence_no');
            $table->string('invoice_type')->nullable();
            $table->string('shipper')->nullable();
            $table->string('consignee')->nullable();
            $table->string('billing_party')->nullable();
            $table->unsignedBigInteger('bill_to_pos')->nullable();
            $table->string('airport_of_loading')->nullable();
            $table->string('airport_of_destination')->nullable();
            $table->date('loading_date')->nullable();
            $table->date('destination_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('proforma_date')->nullable();
            $table->string('client_ref_no')->nullable();
            $table->string('job_no')->nullable();
            $table->date('job_date')->nullable();
            $table->string('hawb_no')->nullable();
            $table->date('hawb_date')->nullable();
            $table->string('mawb_no')->nullable();
            $table->date('mawb_date')->nullable();
            $table->string('packages')->nullable();
            $table->decimal('chargeable_wt', 12, 2)->nullable();
            $table->decimal('gross_wt', 12, 2)->nullable();
            $table->string('commodity')->nullable();
            $table->string('type_of_supply')->nullable();
            $table->string('sb_be_no')->nullable();
            $table->date('sb_be_date')->nullable();
            $table->string('flight_no')->nullable();
            $table->date('flight_date')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('currency', 8)->nullable();
            $table->string('einvoice_status')->nullable();
            $table->string('payment_type', 32)->nullable();
            $table->decimal('paid_amount', 14, 2)->nullable();
            $table->decimal('due_amount', 14, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('shipment_id');
            $table->index(['financial_year_label', 'sequence_no']);
        });

        Schema::create('proforma_invoice_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('description')->nullable();
            $table->string('hsn')->nullable();
            $table->string('remarks')->nullable();
            $table->decimal('qty', 12, 2)->nullable();
            $table->decimal('rate', 14, 2)->nullable();
            $table->string('currency', 8)->nullable();
            $table->decimal('amount', 14, 2)->nullable();
            $table->decimal('exchange_rate', 14, 4)->nullable();
            $table->string('tax_type', 4)->nullable();
            $table->decimal('non_taxable', 14, 2)->nullable();
            $table->decimal('taxable', 14, 2)->nullable();
            $table->decimal('igst_pct', 6, 2)->nullable();
            $table->decimal('igst_amt', 14, 2)->nullable();
            $table->decimal('cgst_pct', 6, 2)->nullable();
            $table->decimal('cgst_amt', 14, 2)->nullable();
            $table->decimal('sgst_pct', 6, 2)->nullable();
            $table->decimal('sgst_amt', 14, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_line_items');
        Schema::dropIfExists('proforma_invoices');
    }
};
