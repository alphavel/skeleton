<?php

require __DIR__.'/../vendor/autoload.php';

use Alphavel\Framework\Application;
use Alphavel\Framework\CoreServiceProvider;

$app = Application::getInstance();

$app->loadConfig(__DIR__.'/../config/app.php');

$app->register(CoreServiceProvider::class);

// Auto-discover plugins from composer packages
// Reads vendor/composer/installed.json once and caches results
$discoveredProviders = $app->discoverProviders();

foreach ($discoveredProviders as $provider) {
    $app->register($provider);
}

// Register additional providers from config (optional)
$configProviders = $app->config('providers', []);

foreach ($configProviders as $provider) {
    $app->register($provider);
}

// Load facades (required for controllers)
$facadeFile = __DIR__ . '/../storage/framework/facades.php';
if (file_exists($facadeFile)) {
    require_once $facadeFile;
}

// Load routes
$router = $app->make('router');
require __DIR__.'/../routes/api.php';

return $app;
