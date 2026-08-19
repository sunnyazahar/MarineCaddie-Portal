<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'DB=' . config('database.connections.mysql.database') . PHP_EOL;

$tables = ['users', 'customers', 'agents', 'suppliers', 'hubs', 'shipments', 'crrs'];

foreach ($tables as $table) {
    $exists = Illuminate\Support\Facades\Schema::hasTable($table) ? 'yes' : 'no';
    echo strtoupper($table) . '_TABLE=' . $exists . PHP_EOL;
}
