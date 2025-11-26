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
        // Create a mock Swoole request object
        $swooleRequest = new class($method, $uri, $data) {
            public array $server;
            public array $get;
            public array $post;
            public array $header;
            private string $body;

            public function __construct(string $method, string $uri, array $data)
            {
                $this->server = [
                    'request_method' => $method,
                    'request_uri' => $uri,
                ];
                $this->get = $method === 'GET' ? $data : [];
                $this->post = $method === 'POST' ? $data : [];
                $this->header = [];
                $this->body = json_encode($data);
            }

            public function rawContent(): string
            {
                return $this->body;
            }
        };

        // Create request from mock Swoole request
        $request = (new Request())->createFromSwoole($swooleRequest);
        
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
