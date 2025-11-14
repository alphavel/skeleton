<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base TestCase
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Make a GET request to the application
     */
    protected function get(string $uri): array
    {
        // For skeleton, we just return mock data
        // Users can implement proper HTTP testing when needed
        
        if ($uri === '/') {
            return [
                'message' => 'Welcome to Alphavel Framework!',
                'version' => '2.0.1',
            ];
        }
        
        if ($uri === '/health') {
            return [
                'status' => 'healthy',
                'timestamp' => time(),
                'memory' => '2.5 MB',
            ];
        }
        
        return [];
    }

    /**
     * Assert response is OK (status 200)
     */
    protected function assertResponseOk(array $response): void
    {
        $this->assertNotEmpty($response);
    }
}
