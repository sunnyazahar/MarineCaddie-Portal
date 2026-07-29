<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ensure PHP can create temp files under XAMPP/Apache (daemon user).
$appTmp = __DIR__.'/../storage/framework/tmp';
if (! is_dir($appTmp)) {
    @mkdir($appTmp, 0777, true);
}
if (is_dir($appTmp) && is_writable($appTmp)) {
    putenv('TMPDIR='.$appTmp);
    putenv('TMP='.$appTmp);
    putenv('TEMP='.$appTmp);
}

// Support subdirectory hosting under /laravel without prefixing route definitions.
if (isset($_SERVER['REQUEST_URI'])) {
    $uri = preg_replace('#^/laravel#', '', $_SERVER['REQUEST_URI']) ?: '/';
    $uri = str_replace('/./', '/', $uri);
    if ($uri === '/.' || $uri === '' || substr($uri, -2) === '/.') {
        $uri = '/';
    }
    $_SERVER['REQUEST_URI'] = $uri;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
