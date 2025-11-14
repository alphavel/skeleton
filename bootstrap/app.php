<?php

require __DIR__.'/../vendor/autoload.php';

use Alphavel\Framework\Application;
use Alphavel\Framework\CoreServiceProvider;

// Check Swoole extension
if (!extension_loaded('swoole')) {
    echo "\n⚠️  Swoole extension not found.\n";
    echo "Alphavel requires Swoole for high-performance async operations.\n\n";
    echo "📦 Install Swoole:\n";
    echo "   Ubuntu/Debian: sudo apt-get install php-swoole\n";
    echo "   macOS: pecl install swoole\n";
    echo "   Or use Docker: docker run -p 9999:9999 alphavel-app\n\n";
    echo "📖 Docs: https://github.com/swoole/swoole-src\n\n";
    exit(1);
}

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
