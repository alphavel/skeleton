<?php

/**
 * Core Only Configuration (Maximum Performance)
 * 
 * Performance: 520k req/s
 * Memory: 0.3MB
 * 
 * Use case: API Gateway, Reverse Proxy, Health Checks
 */

return [
    'debug' => env('APP_DEBUG', false),
    'timezone' => 'UTC',
    
    // No plugins = Maximum performance!
    'providers' => [],
    
    'server' => [
        'host' => env('SERVER_HOST', '0.0.0.0'),
        'port' => (int) env('SERVER_PORT', 9501),
        'workers' => (int) env('SWOOLE_WORKERS', swoole_cpu_num() * 2),
        'reactors' => (int) env('SWOOLE_REACTORS', swoole_cpu_num() * 2),
        'max_coroutine' => (int) env('SWOOLE_MAX_COROUTINE', 100000),
        'max_connections' => (int) env('SWOOLE_MAX_CONN', 10000),
    ],
];
