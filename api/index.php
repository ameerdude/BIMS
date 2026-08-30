<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * Vercel Entry Point
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Configure storage paths for Vercel's ephemeral filesystem
// Vercel serverless functions have a writable /tmp directory
$tmpBase = '/tmp';

// Create required storage directories in /tmp
$dirs = [
    $tmpBase . '/storage/framework/views',
    $tmpBase . '/storage/framework/cache/data',
    $tmpBase . '/storage/framework/sessions',
    $tmpBase . '/storage/logs',
    $tmpBase . '/storage/app/public',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Point Laravel's storage to /tmp at runtime
$_SERVER['APP_STORAGE_PATH'] = $tmpBase . '/storage';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = (require_once __DIR__.'/../bootstrap/app.php');

// Override storage path for Vercel's writable /tmp
$app->useStoragePath($tmpBase . '/storage');

$app->handleRequest(Request::capture());
