<?php

use Illuminate\Http\Request;

// Debug: Test if this file is reached
if (isset($_GET['debug'])) {
    die(json_encode([
        'message' => 'API endpoint reached', 
        'path' => $_SERVER['REQUEST_URI'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'query_string' => $_SERVER['QUERY_STRING'] ?? ''
    ]));
}

// Vercel Serverless Bootstrap untuk Laravel
define('LARAVEL_START', microtime(true));

// Setup environment untuk Vercel SEBELUM semua bootstrap
$_ENV['VERCEL'] = '1';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['LOG_LEVEL'] = 'error';
$_ENV['LOG_DEPRECATIONS_CHANNEL'] = 'null';
$_ENV['CACHE_DRIVER'] = 'array';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['QUEUE_CONNECTION'] = 'sync';

// Setup writable directories SEBELUM Laravel bootstrap
$dirs = ['/tmp/storage', '/tmp/storage/logs', '/tmp/storage/framework', '/tmp/storage/framework/cache', '/tmp/storage/framework/sessions', '/tmp/storage/framework/views', '/tmp/storage/app'];
foreach ($dirs as $dir) { @mkdir($dir, 0755, true); }

// Skip maintenance mode check untuk Vercel (karena menggunakan storage path)
// if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
//     require $maintenance;
// }

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/../bootstrap/app.php';

// Set storage path setelah app dibuat tapi sebelum handleRequest
$app->useStoragePath('/tmp/storage');

// Debug Laravel routes
if (isset($_GET['routes'])) {
    $routes = $app->make('router')->getRoutes();
    $apiRoutes = [];
    foreach ($routes as $route) {
        if (str_starts_with($route->uri(), 'api/')) {
            $apiRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'action' => $route->getActionName()
            ];
        }
    }
    die(json_encode(['api_routes' => $apiRoutes]));
}

$app->handleRequest(Request::capture());