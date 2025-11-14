<?php

/**
 * Application Configuration
 */

return [
    // Application name
    'name' => env('APP_NAME', 'Alphavel App'),
    
    // Environment: production, development, testing
    'env' => env('APP_ENV', 'development'),
    
    // Debug mode
    'debug' => env('APP_DEBUG', true),
    
    // Swoole Server Configuration
    'server' => [
        'host' => env('SERVER_HOST', '0.0.0.0'),
        'port' => env('SERVER_PORT', 9501),
        'workers' => env('SERVER_WORKERS', 4),
    ],
    
    // Service Providers
    // Add providers here when installing packages:
    // Example: Alphavel\Database\DatabaseServiceProvider::class
    'providers' => [],
];
