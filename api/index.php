<?php

define('LARAVEL_START', microtime(true));

// Force HTTPS APP_URL BEFORE bootstrap (Vercel auto-injects http://)
putenv('APP_URL=https://bimss.vercel.app');
$_ENV['APP_URL'] = 'https://bimss.vercel.app';
$_SERVER['APP_URL'] = 'https://bimss.vercel.app';

// Maintenance mode check (same as public/index.php)
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Override storage path for Vercel's writable /tmp
$app->useStoragePath('/tmp/storage');

// Handle the request
$app->handleRequest(\Illuminate\Http\Request::capture());
