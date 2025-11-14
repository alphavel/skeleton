<?php

/**
 * CRUD API Configuration (Core + Database)
 * 
 * Performance: 410k req/s
 * Memory: 2MB
 * 
 * Use case: REST API with database operations
 */

return [
    'debug' => env('APP_DEBUG', false),
    'timezone' => 'UTC',
    
    'providers' => [
        'Alphavel\Database\DatabaseServiceProvider',
    ],
    
    'database' => [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'port' => (int) env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'alphavel'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'persistent' => env('DB_PERSISTENT', false),
    ],
    
    'server' => [
        'host' => env('SERVER_HOST', '0.0.0.0'),
        'port' => (int) env('SERVER_PORT', 9501),
        'workers' => (int) env('SWOOLE_WORKERS', swoole_cpu_num() * 2),
        'reactors' => (int) env('SWOOLE_REACTORS', swoole_cpu_num() * 2),
        'max_coroutine' => (int) env('SWOOLE_MAX_COROUTINE', 100000),
        'max_connections' => (int) env('SWOOLE_MAX_CONN', 10000),
    ],
];
