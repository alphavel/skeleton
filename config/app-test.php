<?php

return [
    'debug' => true,
    'name' => 'alphavel',
    'env' => 'testing',
    
    'server' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'workers' => 4,
        'reactors' => 4,
        'max_coroutine' => 100000,
        'max_connections' => 10000,
    ],
    
    'cache' => [
        'size' => 1024,
        'value_size' => 4096,
    ],
    
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'alphavel',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'options' => [],
    ],
    
    'logging' => [
        'path' => __DIR__.'/../storage/logs',
    ],
];
