<?php

namespace App\Services\Installer;

use App\Services\CompanySettingsService;
use Illuminate\Validation\Rules\Password;

/** Validation rules shared by the /install wizard and `php artisan app:install`. */
final class InstallerValidation
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function databaseRules(): array
    {
        return [
            'db_host' => ['required', 'string', 'max:255', 'regex:' . DatabaseProvisioner::HOST_PATTERN],
            'db_port' => ['required', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'regex:' . DatabaseProvisioner::NAME_PATTERN],
            'db_username' => ['required', 'string', 'max:80'],
            'db_password' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(bool $confirmPassword = false): array
    {
        $adminPassword = ['required', 'string', 'max:255', Password::min(8)->mixedCase()->numbers()];
        if ($confirmPassword) {
            $adminPassword[] = 'confirmed';
        }

        return [
            'company_name' => ['required', 'string', 'max:150'],
            'company_logo' => CompanySettingsService::LOGO_RULES,
            ...self::databaseRules(),
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => $adminPassword,
            'app_url' => ['required', 'url:http,https', 'max:255'],
        ];
    }
}
