<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('company_settings')) {
            return;
        }

        // Single-row table: branding + company details shown on screens, mails and PDFs.
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 150);
            $table->string('legal_name', 200)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('bank_account_name', 200)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_iban', 50)->nullable();
            $table->string('bank_swift', 20)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('bank_city_country', 150)->nullable();
            $table->json('invoice_notes')->nullable();
            $table->boolean('otp_enabled')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
