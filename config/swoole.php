<?php

return [
    'server' => [
        'host' => env('SERVER_HOST', '0.0.0.0'),
        'port' => env('SERVER_PORT', 9999),
        
        // 🚀 PERFORMANCE OPTIMIZED: Workers = CPU × 2 (maximum throughput)
        // For HTTP/REST APIs, this provides optimal parallelism
        'workers' => env('SERVER_WORKERS', function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 8),
        'reactors' => env('SERVER_REACTORS', function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 8),
        
        // 🚀 PERFORMANCE OPTIMIZED: Never restart workers (max throughput)
        // Workers only restart if they crash, not after N requests
        // Set to 10000 if you suspect memory leaks
        'max_request' => env('SERVER_MAX_REQUEST', 0),
        
        // 🚀 PERFORMANCE CRITICAL: PROCESS mode for maximum throughput (+300% performance!)
        // PROCESS: Multi-process, uses all CPU cores - RECOMMENDED for production
        // BASE: Single process + coroutines, limited to 1 core - only for development
        'mode' => env('SERVER_MODE', 'process'), // 'process' (production) or 'base' (dev only)
        
        'dispatch_mode' => 1, // 1: Round robin, 3: Concurrent
        'max_coroutine' => 100000,
        'max_connections' => 10000,
        
        // 🚀 PERFORMANCE: Enable coroutines for async operations
        'enable_coroutine' => true,
        
        // 🚀 PRODUCTION: Minimal logging (reduces I/O overhead)
        'log_level' => env('SWOOLE_LOG_LEVEL', SWOOLE_LOG_ERROR),
        'log_file' => env('SWOOLE_LOG_FILE', __DIR__ . '/../storage/logs/swoole.log'),
    ],
];
