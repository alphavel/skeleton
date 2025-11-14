<?php

/**
 * CLI Configuration
 * For command-line operations without Swoole
 */

return [
    'name' => 'alphavel',
    'env' => 'development',
    'debug' => true,
    
    'database' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', 'mysql'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_NAME', 'myapp'),
        'username' => env('DB_USER', 'user'),
        'password' => env('DB_PASS', 'pass'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],
    
    'server' => [
        'workers' => 16,
        'host' => '0.0.0.0',
        'port' => 9501,
    ],
    
    'providers' => [
        Alphavel\Cache\CacheServiceProvider::class,
        Alphavel\Database\DatabaseServiceProvider::class,
        Alphavel\Events\EventServiceProvider::class,
        Alphavel\Logging\LoggingServiceProvider::class,
        Alphavel\Validation\ValidationServiceProvider::class,
    ],
];
