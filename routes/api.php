<?php

use Alphavel\Framework\Router;
use Alphavel\Framework\Response;

/** @var Router $router */

// Welcome endpoint
$router->get('/', function () {
    return Response::make()->json([
        'message' => 'Welcome to Alphavel Framework!',
        'version' => '1.0.0',
        'documentation' => 'https://alphavel.dev/docs'
    ]);
});

// Health check endpoint
$router->get('/health', function () {
    return Response::make()->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s')
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

// Benchmark routes - High-performance database examples
$router->get('/api/benchmark/user/{id}', 'App\Controllers\BenchmarkController@getUser');
$router->get('/api/benchmark/users', 'App\Controllers\BenchmarkController@listUsers');
$router->get('/api/benchmark/stats', 'App\Controllers\BenchmarkController@stats');
$router->get('/api/benchmark/search', 'App\Controllers\BenchmarkController@search');
$router->post('/api/benchmark/bulk-users', 'App\Controllers\BenchmarkController@bulkInsert');
$router->get('/api/benchmark/users-by-ids', 'App\Controllers\BenchmarkController@usersByIds');
$router->get('/api/benchmark/compare', 'App\Controllers\BenchmarkController@compare');
