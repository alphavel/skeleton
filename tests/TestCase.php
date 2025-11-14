<?php

namespace Tests;

use Alphavel\Framework\Application;
use Alphavel\Framework\Request;
use Alphavel\Framework\Response;
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
     * Make a GET request
     */
    protected function get(string $uri): Response
    {
        return $this->call('GET', $uri);
    }

    /**
     * Make a POST request
     */
    protected function post(string $uri, array $data = []): Response
    {
        return $this->call('POST', $uri, $data);
    }

    /**
     * Make a request to the application
     */
    protected function call(string $method, string $uri, array $data = []): Response
    {
        // Set up request environment
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_POST = $method === 'POST' ? $data : [];
        $_GET = $method === 'GET' ? $data : [];

        // Create request and handle
        $request = Request::capture();
        
        return $this->app->handle($request);
    }

    /**
     * Assert response status is 200
     */
    protected function assertResponseOk(Response $response): void
    {
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Assert response contains JSON
     */
    protected function assertJsonResponse(Response $response): array
    {
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        $this->assertNotNull($data, 'Response is not valid JSON');
        
        return $data;
    }

    /**
     * Get JSON from response
     */
    protected function json(Response $response): array
    {
        return json_decode($response->getContent(), true) ?? [];
    }
}
