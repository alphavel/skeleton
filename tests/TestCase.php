<?php

namespace Tests;

use Alphavel\Framework\Application;
use Alphavel\Framework\Request;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base TestCase
 */
abstract class TestCase extends BaseTestCase
{
    protected Application $app;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Bootstrap application
        $this->app = require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Make a GET request to the application
     */
    protected function get(string $uri): array
    {
        // Create request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '9501';
        
        $request = Request::capture();
        
        // Handle request
        $response = $this->app->handle($request);
        
        // Get response content
        $content = $response->getContent();
        
        // Parse JSON
        return json_decode($content, true) ?? [];
    }

    /**
     * Assert response is OK (status 200)
     */
    protected function assertResponseOk(array $response): void
    {
        $this->assertNotEmpty($response);
    }
}
