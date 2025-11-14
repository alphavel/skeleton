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
     * Show framework version and info
     */
    public function index(): Response
    {
        $composerPath = dirname(__DIR__, 2) . '/composer.json';
        $composerJson = json_decode(file_get_contents($composerPath), true);
        
        return Response::make()->json([
            'framework' => 'Alphavel Framework',
            'version' => '2.0.1',
            'php' => PHP_VERSION,
            'packages' => [
                'alphavel/alphavel' => $composerJson['require']['alphavel/alphavel'] ?? 'unknown',
                'alphavel/cache' => $composerJson['require']['alphavel/cache'] ?? 'unknown',
                'alphavel/database' => $composerJson['require']['alphavel/database'] ?? 'unknown',
                'alphavel/events' => $composerJson['require']['alphavel/events'] ?? 'unknown',
                'alphavel/logging' => $composerJson['require']['alphavel/logging'] ?? 'unknown',
                'alphavel/validation' => $composerJson['require']['alphavel/validation'] ?? 'unknown',
                'alphavel/support' => $composerJson['require']['alphavel/support'] ?? 'unknown',
            ],
            'message' => 'Welcome to Alphavel Framework! 🚀',
            'docs' => 'https://github.com/alphavel/alphavel',
        ]);
    }
}
