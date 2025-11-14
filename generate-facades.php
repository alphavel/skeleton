<?php

/**
 * Generate Facades
 * Run this script to generate facade cache without Swoole
 */

require __DIR__ . '/vendor/autoload.php';

use Alphavel\Framework\Application;
use Alphavel\Framework\CoreServiceProvider;

$app = Application::getInstance();

// Load CLI config (without Swoole)
echo "📂 Loading config...\n";
$app->loadConfig(__DIR__ . '/config/app-cli.php');
echo "   Config loaded: " . json_encode($app->config('name')) . "\n";
echo "   Providers in config: " . count($app->config('providers', [])) . "\n";

// Register CoreServiceProvider
echo "📦 Registering CoreServiceProvider...\n";
$app->register(CoreServiceProvider::class);

// Discover and register packages
echo "🔍 Discovering providers...\n";
$discoveredProviders = $app->discoverProviders();
echo "   Found: " . count($discoveredProviders) . " providers\n";
foreach ($discoveredProviders as $provider) {
    echo "   - $provider\n";
    $app->register($provider);
}

// Register providers from config
echo "📝 Loading config providers...\n";
$configProviders = $app->config('providers', []);
echo "   Found: " . count($configProviders) . " providers\n";
foreach ($configProviders as $provider) {
    echo "   - $provider\n";
    $app->register($provider);
}

// Boot all providers (this generates facades)
echo "🔄 Booting providers...\n";
$app->boot();

$facadeFile = __DIR__ . '/storage/framework/facades.php';
if (file_exists($facadeFile)) {
    echo "✅ Facades generated successfully!\n";
    echo "📁 File: storage/framework/facades.php\n";
    echo "📊 Size: " . filesize($facadeFile) . " bytes\n";
} else {
    echo "❌ Facades were NOT generated!\n";
    echo "📁 Expected: $facadeFile\n";
    
    // Check storage directory
    $storageDir = __DIR__ . '/storage/framework';
    if (is_dir($storageDir)) {
        echo "✅ Directory exists: $storageDir\n";
        echo "📂 Contents: " . implode(', ', scandir($storageDir)) . "\n";
    } else {
        echo "❌ Directory NOT found: $storageDir\n";
    }
}
