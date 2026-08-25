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
            $table->timestamps();
        });

        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('hub_name');
            $table->string('code')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name');
            $table->string('code')->nullable();
            $table->string('contact_person')->nullable();
            $table->text('agent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district_state')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->text('office_address')->nullable();
            $table->string('agent_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
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
            $table->foreignId('office_id')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->timestamps();
        });

        Schema::create('customer_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('account_manager_id')->nullable();
            $table->foreignId('accounting_user_id')->nullable();
        });

        Schema::create('customer_vessels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('vessel')->nullable();
            $table->timestamps();
        });

        Schema::create('crrs', function (Blueprint $table) {
            $table->id();
            $table->string('stock_number')->unique();
            $table->string('vessel_name')->nullable();
            $table->string('content')->default('Shipspares');
            $table->string('supplier')->nullable();
            $table->string('hub_agent')->nullable();
            $table->string('priority')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->json('flags')->nullable();
            $table->boolean('accept')->default(false);
            $table->timestamps();
        });

        Schema::create('crr_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id');
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->string('departure')->nullable();
            $table->string('consignee')->nullable();
            $table->string('service')->nullable();
            $table->date('deadline_arrival')->nullable();
            $table->date('pre_alert_reminder')->nullable();
            $table->foreignId('account_manager_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamps();
        });

        Schema::create('shipment_crr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('crr_id');
        });

        Schema::create('shipment_irregularities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->string('status')->nullable();
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
            'shipment_irregularities',
            'shipment_crr',
            'shipments',
            'crr_packages',
            'crrs',
            'customer_vessels',
            'customer_responsibles',
            'customers',
            'contacts',
            'ports',
            'agents',
            'suppliers',
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
