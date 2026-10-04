<?php

/*
|--------------------------------------------------------------------------
| Master setup (APP_MODE=master)
|--------------------------------------------------------------------------
|
| A master copy of this project creates a new folder per customer, copies
| the application into it and hands the wizard data to that copy's
| installer. Only used when APP_MODE=master.
|
| password_hash: bcrypt hash set with `php artisan master:password`.
| source_path:   project copied into new folders (default: this copy).
| target_root:   folder in which customer folders are created (default:
|                the parent of this project, e.g. htdocs or public_html).
| target_url:    web address of target_root (default: this master's URL
|                without its own folder, e.g. https://example.com).
|
*/

return [
    'password_hash' => env('MASTER_PASSWORD_HASH'),

    'source_path' => env('MASTER_SOURCE_PATH'),

    'target_root' => env('MASTER_TARGET_ROOT'),

    'target_url' => env('MASTER_TARGET_URL'),

    'session_idle_minutes' => (int) env('MASTER_SESSION_IDLE_MINUTES', 60),

    // Folder names that can never be used for a customer.
    'reserved_folders' => [
        'admin', 'api', 'app', 'assets', 'bin', 'cgi-bin', 'dashboard', 'install', 'laravel',
        'mail', 'master', 'phpmyadmin', 'public', 'setup', 'storage', 'vendor', 'webalizer',
        'www', 'xampp',
    ],
];
