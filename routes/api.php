<?php

use Alphavel\Framework\Router;
use Alphavel\Framework\Response;

/** @var Router $router */

// Home route - Shows framework version and info
$router->get('/', 'App\Controllers\HomeController@index');

// Health check endpoint
$router->get('/health', 'App\Controllers\HomeController@health');

// Benchmark routes - High-performance database examples
$router->get('/api/benchmark/user/{id}', 'App\Controllers\BenchmarkController@getUser');
$router->get('/api/benchmark/users', 'App\Controllers\BenchmarkController@listUsers');
$router->get('/api/benchmark/stats', 'App\Controllers\BenchmarkController@stats');
$router->get('/api/benchmark/search', 'App\Controllers\BenchmarkController@search');
$router->post('/api/benchmark/bulk-users', 'App\Controllers\BenchmarkController@bulkInsert');
$router->get('/api/benchmark/users-by-ids', 'App\Controllers\BenchmarkController@usersByIds');
$router->get('/api/benchmark/compare', 'App\Controllers\BenchmarkController@compare');
