<?php

use Alphavel\Framework\Router;

/** @var Router $router */

// Home route - Shows framework version and info
$router->get('/', 'App\Controllers\HomeController@index');

// Health check endpoint
$router->get('/health', fn () => \Alphavel\Framework\Response::success([
    'status' => 'healthy',
    'timestamp' => time(),
    'memory' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
]));
