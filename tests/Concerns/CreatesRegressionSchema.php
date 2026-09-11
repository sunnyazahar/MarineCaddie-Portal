<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

trait CreatesRegressionSchema
{
    /**
     * Hard stop: never create/drop regression tables against MySQL/MariaDB
     * (local `saf`, Hostinger production, or any remote DB). SQLite :memory: only.
     */
    protected function assertSafeTestingDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException(
                'Regression schema refused: APP_ENV must be [testing]. '
                .'Run tests via `composer test` or `php artisan test` (loads phpunit.xml).'
            );
        }

        $connection = (string) config('database.default');
        $driver = (string) config("database.connections.{$connection}.driver");
        $database = (string) config("database.connections.{$connection}.database");
        $host = (string) config("database.connections.{$connection}.host", '');

        if (in_array($driver, ['mysql', 'mariadb', 'pgsql', 'sqlsrv'], true)) {
            throw new RuntimeException(
                "Regression schema refused: driver [{$driver}] host [{$host}] database [{$database}]. "
                .'PHPUnit must use SQLite :memory: from phpunit.xml — never drop tables on MySQL.'
            );
        }

        $isSqliteMemory = $driver === 'sqlite' && (
            $database === ':memory:'
            || $database === ''
            || str_contains($database, ':memory:')
        );

        if (! $isSqliteMemory) {
            throw new RuntimeException(
                "Regression schema refused: connection [{$connection}] driver [{$driver}] database [{$database}]. "
                .'Use phpunit.xml SQLite :memory: only.'
            );
        }
    }

    protected function createRegressionSchema(): void
    {
        $this->assertSafeTestingDatabase();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->string('login_otp_hash', 64)->nullable();
            $table->timestamp('login_otp_expires_at')->nullable();
            $table->timestamp('login_otp_sent_at')->nullable();
            $table->unsignedTinyInteger('otp_failed_attempts')->default(0);
            $table->timestamp('otp_blocked_until')->nullable();
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('currency')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('administration_change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type');
            $table->unsignedBigInteger('loggable_id');
            $table->foreignId('user_id')->nullable();
            $table->string('field')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('office_name');
            $table->string('office_short_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('eori_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->text('postal_address')->nullable();
            $table->string('postal_city')->nullable();
            $table->string('postal_district_state')->nullable();
            $table->string('postal_zip_code')->nullable();
            $table->foreignId('office_country_id')->nullable();
            $table->string('invoicing_currency')->nullable();
            $table->string('reporting_currency')->nullable();
            $table->string('vat_rates')->nullable();
            $table->string('vat_country_specific_name')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('invoicing_emails')->nullable();
            $table->text('heading_invoice')->nullable();
            $table->text('information_invoice')->nullable();
            $table->boolean('use_vat_check')->default(false);
            $table->boolean('show_imo')->default(false);
            $table->boolean('enable_reader')->default(false);
            $table->string('status')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('hub_name');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('customer_number_fm')->nullable();
            $table->string('code')->nullable();
            $table->string('code_description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_gts_company')->default(false);
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            $table->boolean('show_pre_alert')->default(false);
            $table->text('hub_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('port_code')->nullable();
            $table->text('office_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_district_state')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->string('office_country')->nullable();
            $table->string('eori_number')->nullable();
            $table->string('un_locode')->nullable();
            $table->boolean('hide_in_portal')->nullable()->default(false);
            $table->text('portal_remarks')->nullable();
            $table->string('portal_email')->nullable();
            $table->string('invoicing_name')->nullable();
            $table->text('invoicing_address')->nullable();
            $table->string('invoicing_city')->nullable();
            $table->string('invoicing_district')->nullable();
            $table->string('invoicing_zip')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('emails_for_invoicing')->nullable();
            $table->string('emails_for_invoicing_cc')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('invoicing_frequency')->nullable();
            $table->string('billing_currency_outgoing')->nullable();
            $table->string('payment_terms_outgoing')->nullable();
            $table->string('billing_currency_incoming')->nullable();
            $table->string('payment_terms_incoming')->nullable();
            $table->string('agreement_type')->nullable();
            $table->decimal('rebate_percentage', 8, 2)->nullable();
            $table->json('export_services')->nullable();
            $table->json('import_services')->nullable();
            $table->json('export_emails')->nullable();
            $table->json('import_emails')->nullable();
            $table->string('stock_item_changed_emails')->nullable();
            $table->string('quote_requests_emails')->nullable();
            $table->boolean('coc_signed')->default(false);
            $table->boolean('sop_implemented')->default(false);
            $table->date('coc_signed_date')->nullable();
            $table->string('responsible_manager')->nullable();
            $table->string('scan_gun_login')->nullable();
            $table->string('scan_gun_password')->nullable();
            $table->date('agreement_start_date')->nullable();
            $table->date('agreement_expiry_date')->nullable();
            $table->decimal('minimal_cbm', 10, 2)->nullable();
            $table->decimal('minimal_weight', 10, 2)->nullable();
            $table->unsignedInteger('free_storage_days')->nullable();
            $table->decimal('cbm_charge_usd', 10, 2)->nullable();
            $table->boolean('scangun_photo_taking')->default(false);
            $table->boolean('scangun_detailed_shipment_out')->default(false);
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('code')->nullable();
            $table->string('code_description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            $table->boolean('show_pre_alert')->default(false);
            $table->text('agent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('port_code')->nullable();
            $table->text('office_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_district_state')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->unsignedBigInteger('office_country_id')->nullable();
            $table->string('eori_number')->nullable();
            $table->string('un_locode')->nullable();
            $table->string('agent_type')->nullable();
            $table->string('invoicing_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_district_state')->nullable();
            $table->string('billing_zip_code')->nullable();
            $table->unsignedBigInteger('billing_country_id')->nullable();
            $table->string('invoicing_emails')->nullable();
            $table->string('invoicing_emails_cc')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('invoicing_frequency')->nullable();
            $table->boolean('applies_to_rebate')->default(false);
            $table->decimal('rebate_percentage', 8, 2)->nullable();
            $table->string('outgoing_currency')->nullable();
            $table->string('outgoing_payment_terms')->nullable();
            $table->string('incoming_currency')->nullable();
            $table->string('incoming_payment_terms')->nullable();
            $table->boolean('coc_signed')->default(false);
            $table->boolean('sop_implemented')->default(false);
            $table->date('coc_signed_date')->nullable();
            $table->string('responsible_manager')->nullable();
            $table->boolean('calculate_sell_rates')->default(false);
            $table->decimal('purchase_rate', 10, 2)->nullable();
            $table->decimal('sell_rate', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->nullable();
            $table->string('export_email_services')->nullable();
            $table->string('import_email_services')->nullable();
            $table->string('status_changed_emails')->nullable();
            $table->string('stock_item_changed_emails')->nullable();
            $table->string('quote_requests_emails')->nullable();
            $table->string('scangun_login')->nullable();
            $table->string('scangun_password')->nullable();
            $table->boolean('scangun_enable_picture')->default(false);
            $table->boolean('scangun_enable_detailed_shipment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('phone_number')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            $table->text('supplier_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('port_code')->nullable();
            $table->text('office_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_district_state')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->unsignedBigInteger('office_country_id')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('eori_number')->nullable();
            $table->string('currency')->nullable();
            $table->string('un_locode')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('other_companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('code')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('iata_code')->nullable();
            $table->string('un_locode')->nullable();
            $table->string('port_name')->nullable();
            $table->string('city')->nullable();
            $table->string('country_name')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_main_contact')->default(false);
            $table->foreignId('office_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('hub_id')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('other_company_id')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->boolean('is_cc_enabled')->default(false);
            $table->string('status')->nullable();
            $table->string('category')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->string('internal_shipment')->nullable();
            $table->text('remarks')->nullable();
            $table->text('special_considerations')->nullable();
            $table->string('un_locode')->nullable();
            $table->boolean('show_transport_details')->default(false);
            $table->boolean('esea_store_stock_only')->default(false);
            $table->string('logo')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('sales_manager_id')->nullable();
            $table->foreignId('account_manager_id')->nullable();
            $table->foreignId('accounting_user_id')->nullable();
        });

        Schema::create('customer_vessels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('vessel')->nullable();
            $table->string('vessel_name_alias')->nullable();
            $table->string('vessel_imo')->nullable();
            $table->string('shipyard')->nullable();
            $table->string('shipyard_location')->nullable();
            $table->boolean('not_in_transit')->default(false);
            $table->boolean('inactive_vessel')->default(false);
            $table->boolean('sanction_blocked')->default(false);
            $table->boolean('financially_blocked')->default(false);
            $table->boolean('pre_payment_only')->default(false);
            $table->string('customer_vessel_code')->nullable();
            $table->string('vessel_type_alias')->nullable();
            $table->string('po_example')->nullable();
            $table->string('internal_shipment')->nullable();
            $table->string('except_from_hubs')->nullable();
            $table->text('remarks')->nullable();
            $table->string('manager')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('receivers_stocklists')->nullable();
            $table->string('home_consolidation_port')->nullable();
            $table->string('home_delivery_port')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->boolean('contact_stocklists')->default(false);
            $table->boolean('contact_pre_alerts')->default(false);
            $table->boolean('contact_stock_notifications')->default(false);
            $table->boolean('contact_free_storage_notifications')->default(false);
            $table->boolean('contact_offers')->default(false);
            $table->boolean('invoice_vessel_separately')->default(false);
            $table->string('title_invoice_recipient')->nullable();
            $table->string('yearly_customer_reference')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('invoice_recipient_name')->nullable();
            $table->string('invoice_email')->nullable();
            $table->string('invoice_email_cc')->nullable();
            $table->string('currency_code')->nullable();
            $table->unsignedInteger('payment_terms_days')->nullable();
            $table->string('invoice_frequency')->nullable();
            $table->text('invoice_remarks')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('eori_number')->nullable();
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('type')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->string('port_code')->nullable();
        });

        Schema::create('customer_sops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('send_stocklist')->nullable();
            $table->string('onboard_delivery')->nullable();
            $table->string('quotes_prior_to_instructions')->nullable();
            $table->string('agreed_rate')->nullable();
            $table->text('invoicing_procedure')->nullable();
            $table->string('pending_entry')->nullable();
            $table->text('special_pending_routines')->nullable();
            $table->text('other_procedures_comments')->nullable();
        });

        Schema::create('customer_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('notify_stock_items')->nullable();
            $table->boolean('send_automatic_first_mile_email')->default(false);
            $table->string('notify_first_mile_email_sent')->nullable();
            $table->unsignedInteger('shipping_free_storage_days')->nullable();
            $table->decimal('shipping_free_storage_weight', 10, 2)->nullable();
            $table->decimal('shipping_free_storage_volume', 10, 2)->nullable();
            $table->string('notify_free_storage_exceeded')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamps();
        });

        Schema::create('hub_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id');
            $table->string('document_type')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamps();
        });

        Schema::create('hub_pricing_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamps();
        });

        Schema::create('agent_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id');
            $table->string('section')->nullable();
            $table->string('filename')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('office_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id');
            $table->string('bank')->nullable();
            $table->string('currency')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();
            $table->boolean('is_main_account')->default(false);
            $table->timestamps();
        });

        Schema::create('crrs', function (Blueprint $table) {
            $table->id();
            $table->string('stock_number');
            $table->unsignedBigInteger('duplicated_from_crr_id')->nullable()->index();
            $table->string('vessel_name')->nullable();
            $table->string('content')->default('Shipspares');
            $table->string('supplier')->nullable();
            $table->string('hub_agent')->nullable();
            $table->string('hub_code')->nullable();
            $table->string('location')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('customs_value', 12, 2)->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->json('po_numbers')->nullable();
            $table->boolean('is_landed_goods')->default(false);
            $table->string('internal_shipment')->nullable();
            $table->string('transit_type')->nullable();
            $table->string('transit_id')->nullable();
            $table->string('priority')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->json('flags')->nullable();
            $table->boolean('accept')->default(false);
            $table->timestamps();
        });

        Schema::create('crr_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id');
            $table->foreignId('user_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('crr_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id');
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('cbm', 12, 4)->nullable();
            $table->string('warehouse_location')->nullable();
            $table->boolean('is_dgr')->default(false);
            $table->timestamps();
        });

        Schema::create('crr_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id');
            $table->string('type')->nullable();
            $table->decimal('net_value', 12, 2)->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->string('departure')->nullable();
            $table->string('departure_port_code')->nullable();
            $table->string('consignee')->nullable();
            $table->string('consignee_port_code')->nullable();
            $table->text('consignee_address')->nullable();
            $table->string('consignee_city')->nullable();
            $table->string('consignee_district')->nullable();
            $table->string('consignee_zip')->nullable();
            $table->string('consignee_country')->nullable();
            $table->string('consignee_att')->nullable();
            $table->string('consignee_email')->nullable();
            $table->string('location')->nullable();
            $table->string('service')->nullable();
            $table->string('additional_service')->nullable();
            $table->string('customer_reference')->nullable();
            $table->boolean('not_applicable_for_consolidation')->default(false);
            $table->unsignedInteger('repacked_items')->nullable();
            $table->decimal('repacked_weight', 12, 2)->nullable();
            $table->unsignedInteger('stock_repacked_items')->nullable();
            $table->decimal('stock_repacked_weight', 12, 2)->nullable();
            $table->date('deadline_arrival')->nullable();
            $table->date('preferred_shipment_date')->nullable();
            $table->date('vessel_eta')->nullable();
            $table->date('vessel_etd')->nullable();
            $table->date('pre_alert_reminder')->nullable();
            $table->foreignId('account_manager_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->text('special_considerations_destination')->nullable();
            $table->boolean('skip_instruction_dest')->default(false);
            $table->text('comments_departure_hub')->nullable();
            $table->boolean('skip_instruction_hub')->default(false);
            $table->text('comments_consignee')->nullable();
            $table->boolean('skip_prealert')->default(false);
            $table->boolean('project_logistics')->default(false);
            $table->boolean('port_agency')->default(false);
            $table->string('status')->default('Draft');
            $table->json('flags')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->default('document');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
        });

        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
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
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
            $table->unique('shipment_id');
        });

        Schema::create('proforma_invoice_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('description')->nullable();
            $table->string('hsn')->nullable();
            $table->string('remarks')->nullable();
            $table->decimal('qty', 12, 2)->nullable();
            $table->string('qty_type', 32)->nullable();
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

        Schema::create('shipment_crr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('crr_id');
        });

        Schema::create('shipment_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->string('shipment_number')->index();
            $table->unsignedBigInteger('original_crr_id')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('hub_code')->nullable();
            $table->string('vessel_name')->nullable();
            $table->json('po_numbers')->nullable();
            $table->string('supplier')->nullable();
            $table->string('stock_number')->nullable();
            $table->unsignedInteger('pieces_count')->default(0);
            $table->decimal('total_weight', 12, 2)->default(0);
            $table->decimal('total_cbm', 12, 4)->default(0);
            $table->decimal('customs_value', 15, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('status_label')->nullable();
            $table->json('snapshot_data');
            $table->timestamps();
        });

        Schema::create('crr_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('leg_reference')->nullable();
            $table->string('flight_number')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('arrival_time', 5)->nullable();
            $table->timestamps();
        });

        foreach (['shipment_sea_legs', 'shipment_truck_legs', 'shipment_courier_legs'] as $legsTable) {
            Schema::create($legsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipment_id');
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('leg_reference')->nullable();
                $table->string('bill_of_lading')->nullable();
                $table->string('cmr')->nullable();
                $table->string('airway_bill')->nullable();
                $table->string('flight_number')->nullable();
                $table->string('transport_vessel_name')->nullable();
                $table->string('freight_company')->nullable();
                $table->string('carrier')->nullable();
                $table->date('departure_date')->nullable();
                $table->date('arrival_date')->nullable();
                $table->date('etd')->nullable();
                $table->date('eta')->nullable();
                $table->string('arrival_time', 5)->nullable();
                $table->timestamps();
            });
        }

        Schema::create('shipment_release_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('freight_company')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time', 5)->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_hand_carry_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('contact_name')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('arrival_time', 5)->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_on_board_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('departure_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time', 5)->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_irregularities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('user_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('shipment_pre_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->unsignedInteger('version');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('form_hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_pre_alert_reminder_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('category', 32)->default('other');
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('link_label')->nullable();
            $table->string('link_url')->nullable();
            $table->string('icon')->default('comment');
            $table->boolean('is_read')->default(false);
            $table->nullableMorphs('related');
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });

        foreach (['office', 'hub', 'agent', 'supplier'] as $entity) {
            Schema::create("user_{$entity}_assignments", function (Blueprint $table) use ($entity) {
                $table->foreignId('user_id');
                $table->foreignId("{$entity}_id");
                $table->unique(['user_id', "{$entity}_id"]);
            });
        }
    }

    protected function dropRegressionSchema(): void
    {
        $this->assertSafeTestingDatabase();

        Schema::disableForeignKeyConstraints();

        foreach ([
            'user_notifications',
            'administration_change_logs',
            'user_supplier_assignments',
            'user_agent_assignments',
            'user_hub_assignments',
            'user_office_assignments',
            'shipment_pre_alert_reminder_sends',
            'shipment_pre_alerts',
            'shipment_change_logs',
            'shipment_irregularities',
            'shipment_stock_snapshots',
            'shipment_courier_legs',
            'shipment_truck_legs',
            'shipment_sea_legs',
            'shipment_on_board_legs',
            'shipment_hand_carry_legs',
            'shipment_release_legs',
            'shipment_flights',
            'shipment_documents',
            'shipment_crr',
            'proforma_invoice_line_items',
            'proforma_invoices',
            'shipments',
            'crr_documents',
            'crr_change_logs',
            'crr_costs',
            'crr_packages',
            'crrs',
            'office_bank_accounts',
            'agent_documents',
            'hub_pricing_documents',
            'hub_documents',
            'customer_documents',
            'customer_notification_settings',
            'customer_sops',
            'customer_invoice_details',
            'customer_addresses',
            'customer_vessels',
            'customer_responsibles',
            'customers',
            'contacts',
            'ports',
            'agents',
            'suppliers',
            'other_companies',
            'hubs',
            'countries',
            'offices',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }
}
