<?php

return [
    'server' => [
        'host' => env('SERVER_HOST', '0.0.0.0'),
        'port' => env('SERVER_PORT', 9999),
        'workers' => env('SERVER_WORKERS', function_exists('swoole_cpu_num') ? swoole_cpu_num() : 4),
        'reactors' => env('SERVER_REACTORS', function_exists('swoole_cpu_num') ? swoole_cpu_num() : 4),
        'max_request' => 0, // Infinite, prevents restart
        'dispatch_mode' => 1, // 1: Round robin, 3: Concurrent
        'max_coroutine' => 100000,
        'max_connections' => 10000,
    ],
];
