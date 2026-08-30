<?php

define('LARAVEL_START', microtime(true));

// Maintenance mode check (same as public/index.php)
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Force HTTPS APP_URL (Vercel auto-injects http://)
if (!isset($_SERVER['APP_URL']) || str_starts_with($_SERVER['APP_URL'], 'http://')) {
    $_SERVER['APP_URL'] = 'https://bimss.vercel.app';
}
config(['app.url' => $_SERVER['APP_URL']]);

// Override storage path for Vercel's writable /tmp
$app->useStoragePath('/tmp/storage');

// Handle the request
$app->handleRequest(\Illuminate\Http\Request::capture());
