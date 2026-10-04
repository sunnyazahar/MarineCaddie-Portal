<?php

namespace App\Services\Installer;

class RequirementsChecker
{
    public const MIN_PHP = '8.2.0';

    private const EXTENSIONS = ['pdo_mysql', 'mbstring', 'openssl', 'fileinfo', 'gd', 'zip', 'curl', 'dom', 'xml'];

    /**
     * @return list<array{label: string, ok: bool, detail: string}>
     */
    public function check(): array
    {
        $results = [[
            'label' => 'PHP ' . self::MIN_PHP . ' or newer',
            'ok' => version_compare(PHP_VERSION, self::MIN_PHP, '>='),
            'detail' => 'Current: ' . PHP_VERSION,
        ]];

        foreach (self::EXTENSIONS as $extension) {
            $results[] = [
                'label' => 'PHP extension: ' . $extension,
                'ok' => extension_loaded($extension),
                'detail' => extension_loaded($extension) ? 'Loaded' : 'Missing — enable it in the hosting PHP settings',
            ];
        }

        foreach ($this->writablePaths() as $label => $path) {
            $results[] = [
                'label' => 'Writable: ' . $label,
                'ok' => is_dir($path) ? is_writable($path) : false,
                'detail' => is_dir($path) && is_writable($path) ? 'OK' : 'Not writable — set folder permission to 755/775',
            ];
        }

        $envPath = base_path('.env');
        $envWritable = is_file($envPath) ? is_writable($envPath) : is_writable(base_path());
        $results[] = [
            'label' => 'Writable: .env file',
            'ok' => $envWritable,
            'detail' => $envWritable ? 'OK' : 'The installer must be able to create/update .env in the project folder',
        ];

        return $results;
    }

    public function passes(): bool
    {
        foreach ($this->check() as $result) {
            if (! $result['ok']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string, string>
     */
    private function writablePaths(): array
    {
        return [
            'storage' => storage_path(),
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];
    }
}
