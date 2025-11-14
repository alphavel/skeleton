<?php

namespace App\Middlewares;

/**
 * TestAuthMiddleware
 *
 * Middleware para processar requisições
 */
class TestAuthMiddleware
{
    /**
     * Handle incoming request
     *
     * @param  mixed  $request
     * @return mixed
     */
    public function handle($request, callable $next)
    {
        // Execute code before request handling
        // Example: authentication, logging, validation

        // if ($request->input('api_key') !== 'secret') {
        //     return ['error' => 'Unauthorized', 'status' => 401];
        // }

        // Continue to next middleware or controller
        $response = $next($request);

        // Execute code after request handling
        // Example: modify response, add headers

        return $response;
    }
}
