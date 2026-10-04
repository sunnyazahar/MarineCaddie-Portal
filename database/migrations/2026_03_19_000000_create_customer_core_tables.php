<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Base tables that existing installs created outside migrations. Each create is
 * guarded so long-running databases skip it; later migrations add the remaining
 * columns (customers.logo / contact_person / created_by, countries.currency …).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->char('iso_code', 2)->unique('uniq_iso_code');
                $table->char('iso3_code', 3)->nullable()->unique('uniq_iso3_code');
                $table->string('phone_code', 10)->nullable();
                $table->string('flag_url')->nullable();
                $table->string('flag_emoji', 10)->nullable();
                $table->boolean('is_active')->nullable()->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('customer_groups')) {
            Schema::create('customer_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->nullable();
            });
        }

        if (! Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name', 150);
                $table->string('customer_number', 50)->nullable()->index('idx_customer_number');
                $table->unsignedBigInteger('customer_group_id')->nullable()->index('idx_group');
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->unsignedTinyInteger('internal_shipment')->nullable();
                $table->text('remarks')->nullable();
                $table->text('special_considerations')->nullable();
                $table->string('un_locode', 15)->nullable();
                $table->boolean('show_transport_details')->nullable()->default(false);
                $table->boolean('esea_store_stock_only')->nullable()->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->index('idx_customer')->constrained('customers')->cascadeOnDelete();
                $table->enum('type', ['primary', 'postal', 'invoice'])->index('idx_type');
                $table->string('street', 200)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 100)->nullable();
                $table->string('zip_code', 15)->nullable();
                $table->unsignedBigInteger('country_id')->nullable();
                $table->string('port_code', 20)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('customer_responsibles')) {
            Schema::create('customer_responsibles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->unique('uniq_customer_responsible')->constrained('customers')->cascadeOnDelete();
                $table->unsignedBigInteger('sales_manager_id')->nullable();
                $table->unsignedBigInteger('account_manager_id')->nullable();
                $table->unsignedBigInteger('accounting_user_id')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('customer_invoice_details')) {
            Schema::create('customer_invoice_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->unique('uniq_customer_invoice')->constrained('customers')->cascadeOnDelete();
                $table->string('invoice_recipient_name', 150)->nullable();
                $table->string('invoice_email', 150)->nullable();
                $table->string('invoice_email_cc')->nullable();
                $table->char('currency_code', 3)->nullable();
                $table->unsignedSmallInteger('payment_terms_days')->nullable()->default(30);
                $table->string('invoice_frequency', 50)->nullable();
                $table->text('invoice_remarks')->nullable();
                $table->string('vat_number', 50)->nullable();
                $table->string('eori_number', 50)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: on existing installs these tables predate this
        // migration and hold live customer data, so a rollback must not drop them.
    }
};
