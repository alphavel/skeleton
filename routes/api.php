<?php

use Alphavel\Framework\Router;
use Alphavel\Framework\Response;

/** @var Router $router */

// 🚀 RAW ROUTES (Zero Overhead - Ultra Fast)
// Perfect for health checks, metrics, and static responses
// Bypasses entire framework stack for maximum performance

// Health check (for Kubernetes, Docker, load balancers)
$router->raw('/health', ['status' => 'healthy'], 'application/json');

// Readiness probe
$router->raw('/ready', ['ready' => true], 'application/json');

// Plain text response
$router->raw('/ping', 'pong');

// Custom closure with full Swoole control
$router->raw('/metrics', function($request, $response) {
    $stats = swoole_get_server_stats();
    $response->header('Content-Type', 'text/plain');
    $response->end(
        "requests_total {$stats['request_count']}\n" .
        "connections_active {$stats['connection_num']}\n" .
        "workers_active {$stats['worker_num']}\n"
    );
}, 'text/plain');

// 📌 STANDARD ROUTES (Framework Stack)
// Use these when you need Request object, middlewares, or complex logic

// Welcome endpoint
$router->get('/', function () {
    return Response::make()->json([
        'message' => 'Welcome to Alphavel Framework!',
        'version' => '1.0.0',
        'documentation' => 'https://alphavel.github.io/documentation'
    ]);
});

// Example endpoint with parameter
$router->get('/hello/{name}', function ($name) {
    return Response::make()->json([
        'message' => "Hello, {$name}!",
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Example controller routes
$router->get('/example', 'App\Controllers\ExampleController@index');
$router->get('/example/{id}', 'App\Controllers\ExampleController@show');
$router->post('/example', 'App\Controllers\ExampleController@store');
$router->put('/example/{id}', 'App\Controllers\ExampleController@update');
$router->delete('/example/{id}', 'App\Controllers\ExampleController@destroy');
