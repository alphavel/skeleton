<?php

use Alphavel\Framework\Router;
use Alphavel\Framework\Response;

/** @var Router $router */

// Home route - Shows framework version and info
$router->get('/', 'App\Controllers\HomeController@index');

// Health check endpoint
$router->get('/health', 'App\Controllers\HomeController@health');
