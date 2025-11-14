<?php

use Alphavel\Framework\Router;

/** @var Router $router */

$router->get('/', 'App\Controllers\HomeController@index');

$router->get('/health', fn () => \Alphavel\Framework\Response::success([
    'status' => 'healthy',
    'timestamp' => time(),
    'memory' => memory_get_usage(true) / 1024 / 1024 .' MB',
]));

// API routes
$router->get('/api/users', 'App\Controllers\UserController@index');
$router->get('/api/users/{id}', 'App\Controllers\UserController@show');
$router->post('/api/users', 'App\Controllers\UserController@store');
$router->put('/api/users/{id}', 'App\Controllers\UserController@update');
$router->delete('/api/users/{id}', 'App\Controllers\UserController@destroy');
