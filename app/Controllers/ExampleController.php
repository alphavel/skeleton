<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Response;

/**
 * Example Controller usando facades
 */
class ExampleController extends Controller
{
    /**
     * Usando Response builder
     */
    public function index(): Response
    {
        // Dispara evento usando facade global
        Event::fire('controller.accessed', ['controller' => 'Example', 'action' => 'index']);

        return Response::success([
            'message' => 'Example controller with facades',
            'config' => app('config')->get('name', 'alphavel'),
            'timestamp' => time(),
        ]);
    }

    /**
     * Usando Container DI
     */
    public function withDependency(): Response
    {
        // Resolve Database via app() helper
        $db = app('db');

        return Response::success([
            'dependency_injection' => 'working',
            'database' => $db !== null,
        ]);
    }

    /**
     * Usando Pipeline de middleware
     */
    public function protected(): Response
    {
        // Pipeline processa middlewares
        // Configurado em routes.php

        return Response::success([
            'message' => 'Protected route accessed',
            'user' => 'authenticated',
        ]);
    }

    /**
     * Usando Event system
     */
    public function events(): Response
    {
        // Listener
        Event::on('user.created', function ($data) {
            // Log, notificação, etc
            return ['logged' => true];
        });

        // Dispara
        $result = Event::fire('user.created', ['id' => 123, 'name' => 'John']);

        return Response::success($result);
    }

    /**
     * JSON response com status customizado
     */
    public function customStatus(): Response
    {
        return Response::make()
            ->json(['id' => 456, 'name' => 'New Item'], 201)
            ->header('X-Custom-Header', 'Value');
    }

    /**
     * Error response
     */
    public function errorExample(): Response
    {
        return Response::error('Resource not found', 404);
    }
}
