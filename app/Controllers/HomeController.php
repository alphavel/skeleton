<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Response;

/**
 * HomeController
 *
 * Main application controller
 */
class HomeController extends Controller
{
    /**
     * Welcome endpoint
     */
    public function index(): Response
    {
        return Response::make()->json([
            'message' => 'Welcome to Alphavel Framework! 🚀',
            'description' => 'Ultra-fast PHP framework built with Swoole for high-performance applications',
            'version' => '2.0.1',
            'php' => PHP_VERSION,
            'features' => [
                '⚡ Swoole-powered async I/O',
                '🎯 Laravel-style syntax',
                '📦 Modular architecture',
                '🚀 520k+ requests/second',
            ],
            'docs' => 'https://github.com/alphavel/alphavel',
            'get_started' => [
                'database' => 'composer require alphavel/database',
                'cache' => 'composer require alphavel/cache',
                'events' => 'composer require alphavel/events',
                'logging' => 'composer require alphavel/logging',
                'validation' => 'composer require alphavel/validation',
            ]
        ]);
    }
}
