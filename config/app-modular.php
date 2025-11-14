<?php

return [
    'debug' => env('APP_DEBUG', true),
    'timezone' => 'UTC',
    
    // Service providers to load (explicit = zero overhead)
    'providers' => [
        // Add only the plugins you installed:
        // 'Alphavel\Database\DatabaseServiceProvider',
        // 'Alphavel\Cache\CacheServiceProvider',
        // 'Alphavel\Validation\ValidationServiceProvider',
        // 'Alphavel\Events\EventServiceProvider',
        // 'Alphavel\Logging\LoggingServiceProvider',
        // 'Alphavel\Support\SupportServiceProvider',
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
