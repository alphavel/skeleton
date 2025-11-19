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
    
    // Swoole Server Configuration (Otimizado para Performance)
    'server' => [
        'host' => env('SERVER_HOST', '0.0.0.0'),
        'port' => env('SERVER_PORT', 9999),
        
        // Worker Num: 1 worker por CPU core (evita context switching)
        'workers' => env('SERVER_WORKERS', function_exists('swoole_cpu_num') ? swoole_cpu_num() : 4),
        
        // Reactor Num: 1 reactor por CPU core
        'reactors' => env('SERVER_REACTORS', function_exists('swoole_cpu_num') ? swoole_cpu_num() : 4),
        
        // Dispatch Mode: 1=Round Robin, 2=FD Modulo, 3=Concurrent (melhor para APIs stateless)
        'dispatch_mode' => env('SERVER_DISPATCH_MODE', 3),
        
        // Coroutines
        'enable_coroutine' => true,
        'max_coroutine' => env('SERVER_MAX_COROUTINE', 100000),
        
        // Connections
        'max_connections' => env('SERVER_MAX_CONNECTIONS', 10000),
        'max_request' => env('SERVER_MAX_REQUEST', 0), // 0 = unlimited (recomendado)
        
        // TCP Optimizations
        'open_tcp_nodelay' => true,
        'enable_reuse_port' => true,
        
        // Buffer sizes
        'buffer_output_size' => 2 * 1024 * 1024,
        'package_max_length' => 2 * 1024 * 1024,
    ],
    
    // Service Providers
    // Add providers here when installing packages:
    // Example: Alphavel\Database\DatabaseServiceProvider::class
    'providers' => [],
];
