<?php

namespace App\Console\Commands;

use App\Models\ProformaInvoice;
use App\Repositories\Contracts\CompanySettingsRepositoryInterface;
use App\Services\Installer\DatabaseCredentials;
use App\Services\Installer\DatabaseProvisioner;
use App\Services\Installer\InstallerException;
use App\Services\Installer\InstallerService;
use App\Services\Installer\InstallerValidation;
use App\Support\Installation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class InstallApplicationCommand extends Command
{
    protected $signature = 'app:install
                            {--mark-installed : Existing setup: only create the install lock (and a company settings row if missing)}
                            {--company= : Company name used with --mark-installed (defaults to APP_NAME)}';

    protected $description = 'Install the application on an empty database (same steps as the /install web wizard)';

    public function handle(InstallerService $installer, DatabaseProvisioner $database, CompanySettingsRepositoryInterface $settings): int
    {
        if ($this->option('mark-installed')) {
            return $this->markExistingInstall($settings);
        }

        if (Installation::isInstalled()) {
            $this->error('Already installed (' . Installation::lockPath() . ' exists).');

            return self::FAILURE;
        }

        $company = trim((string) $this->ask('Company name'));
        $input = [
            'company_name' => $company,
            'db_host' => (string) $this->ask('Database host', '127.0.0.1'),
            'db_port' => (string) $this->ask('Database port', '3306'),
            'db_database' => (string) $this->ask('Database name', $database->suggestName($company)),
            'db_username' => (string) $this->ask('Database username'),
            'db_password' => (string) $this->secret('Database password'),
            'admin_name' => (string) $this->ask('Admin name'),
            'admin_email' => (string) $this->ask('Admin email'),
            'admin_password' => (string) $this->secret('Admin password (min 8, upper + lower case + number)'),
            'app_url' => (string) $this->ask('Application URL', (string) config('app.url')),
        ];

        $validator = Validator::make($input, InstallerValidation::rules());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        try {
            $installer->install(
                DatabaseCredentials::fromInput($input),
                $input['company_name'],
                ['name' => $input['admin_name'], 'email' => $input['admin_email'], 'password' => $input['admin_password']],
                $input['app_url'],
            );
        } catch (InstallerException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Installed. Log in at ' . rtrim($input['app_url'], '/') . '/login');

        return self::SUCCESS;
    }

    private function markExistingInstall(CompanySettingsRepositoryInterface $settings): int
    {
        if (! Schema::hasTable('company_settings')) {
            $this->error('Run "php artisan migrate --force" first (company_settings table is missing).');

            return self::FAILURE;
        }

        if ($settings->current() === null) {
            $company = trim((string) ($this->option('company') ?: config('app.name')));
            $settings->save(['company_name' => $company !== '' ? $company : 'Portal', 'otp_enabled' => true]);
            $this->info("Company settings created for \"{$company}\" (OTP stays enabled). Complete address, bank and logo in Settings > Company settings.");
        }

        $prefix = $settings->current()?->proforma_prefix === null ? $this->existingProformaPrefix() : null;
        if ($prefix !== null) {
            $settings->save(['proforma_prefix' => $prefix]);
            $this->info("Invoice number prefix kept as \"{$prefix}\" (from the latest proforma invoice).");
        }

        if (! Installation::isInstalled()) {
            Installation::markInstalled(['mode' => 'existing']);
        }

        $this->info('Install lock present: ' . Installation::lockPath());

        return self::SUCCESS;
    }

    /** Prefix of the newest proforma number ("AB-XY26-27-0042" → "AB-XY") so existing numbering continues unchanged. */
    private function existingProformaPrefix(): ?string
    {
        if (! Schema::hasTable('proforma_invoices') || ! Schema::hasColumn('company_settings', 'proforma_prefix')) {
            return null;
        }

        $latest = ProformaInvoice::query()
            ->whereNotNull('financial_year_label')
            ->whereNotNull('sequence_no')
            ->latest('id')
            ->first(['proforma_no', 'financial_year_label', 'sequence_no']);

        if ($latest === null) {
            return null;
        }

        $suffix = $latest->financial_year_label . '-' . sprintf('%04d', (int) $latest->sequence_no);
        $number = (string) $latest->proforma_no;

        if (! str_ends_with($number, $suffix)) {
            return null;
        }

        $prefix = substr($number, 0, -strlen($suffix));

        return preg_match('/^[A-Za-z0-9-]{1,20}$/', $prefix) === 1 ? $prefix : null;
    }
}
