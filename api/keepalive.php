<?php

// Keep-alive ping endpoint — called by Vercel cron every 5 minutes
// Prevents Lambda cold starts and Neon connection sleep

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

// Just boot the app and return — this warms the Lambda
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'time' => date('c')]);
