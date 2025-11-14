<?php

namespace Tests;

use Alphavel\Framework\Loader;
use Alphavel\Framework\Request;
use Alphavel\Framework\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base TestCase
 *
 * Provides helper methods for testing
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear any cached data
        if (false // Facades removidas) {
            // Cache::clear();
        }
    }

    /**
     * Teardown after each test
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Make a GET request
     */
    protected function get(string $uri, array $headers = []): Response
    {
        return $this->makeRequest('GET', $uri, [], $headers);
    }

    /**
     * Make a POST request
     */
    protected function post(string $uri, array $data = [], array $headers = []): Response
    {
        return $this->makeRequest('POST', $uri, $data, $headers);
    }

    /**
     * Make a PUT request
     */
    protected function put(string $uri, array $data = [], array $headers = []): Response
    {
        return $this->makeRequest('PUT', $uri, $data, $headers);
    }

    /**
     * Make a DELETE request
     */
    protected function delete(string $uri, array $headers = []): Response
    {
        return $this->makeRequest('DELETE', $uri, [], $headers);
    }

    /**
     * Make a JSON GET request
     */
    protected function getJson(string $uri, array $headers = []): array
    {
        $headers['Content-Type'] = 'application/json';
        $response = $this->get($uri, $headers);

        return $this->parseJsonResponse($response);
    }

    /**
     * Make a JSON POST request
     */
    protected function postJson(string $uri, array $data = [], array $headers = []): array
    {
        $headers['Content-Type'] = 'application/json';
        $response = $this->post($uri, $data, $headers);

        return $this->parseJsonResponse($response);
    }

    /**
     * Make a JSON PUT request
     */
    protected function putJson(string $uri, array $data = [], array $headers = []): array
    {
        $headers['Content-Type'] = 'application/json';
        $response = $this->put($uri, $data, $headers);

        return $this->parseJsonResponse($response);
    }

    /**
     * Make a JSON DELETE request
     */
    protected function deleteJson(string $uri, array $headers = []): array
    {
        $headers['Content-Type'] = 'application/json';
        $response = $this->delete($uri, $headers);

        return $this->parseJsonResponse($response);
    }

    /**
     * Make request (simulated)
     */
    protected function makeRequest(string $method, string $uri, array $data = [], array $headers = []): Response
    {
        // Create mock request
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_POST = $data;

        foreach ($headers as $key => $value) {
            $_SERVER['HTTP_'.strtoupper(str_replace('-', '_', $key))] = $value;
        }

        $request = new Request;

        // Simulate routing and controller execution
        // This is a simplified version - in reality you'd route through your router

        return Response::success(['message' => 'Test response']);
    }

    /**
     * Parse JSON response
     */
    protected function parseJsonResponse(Response $response): array
    {
        $content = $response->getContent();

        return json_decode($content, true) ?? [];
    }

    /**
     * Assert response status
     */
    protected function assertResponseStatus(int $expected, Response $response): void
    {
        $this->assertEquals($expected, $response->getStatusCode());
    }

    /**
     * Assert response has key
     */
    protected function assertResponseHasKey(string $key, array $response): void
    {
        $this->assertArrayHasKey($key, $response);
    }

    /**
     * Assert database has record
     */
    protected function assertDatabaseHas(string $table, array $data): void
    {
        $db = Loader::load('Database');

        $conditions = [];
        $params = [];

        foreach ($data as $column => $value) {
            $conditions[] = "$column = ?";
            $params[] = $value;
        }

        $sql = "SELECT COUNT(*) as count FROM $table WHERE ".implode(' AND ', $conditions);
        $result = $db->query($sql, $params);

        $this->assertGreaterThan(0, $result[0]['count'], "Failed asserting that table '$table' contains matching record");
    }

    /**
     * Assert database missing record
     */
    protected function assertDatabaseMissing(string $table, array $data): void
    {
        $db = Loader::load('Database');

        $conditions = [];
        $params = [];

        foreach ($data as $column => $value) {
            $conditions[] = "$column = ?";
            $params[] = $value;
        }

        $sql = "SELECT COUNT(*) as count FROM $table WHERE ".implode(' AND ', $conditions);
        $result = $db->query($sql, $params);

        $this->assertEquals(0, $result[0]['count'], "Failed asserting that table '$table' does not contain matching record");
    }

    /**
     * Assert database count
     */
    protected function assertDatabaseCount(string $table, int $expected): void
    {
        $db = Loader::load('Database');
        $result = $db->query("SELECT COUNT(*) as count FROM $table");

        $this->assertEquals($expected, $result[0]['count'], "Failed asserting that table '$table' has $expected records");
    }

    /**
     * Create a mock user (helper)
     */
    protected function createUser(array $attributes = []): array
    {
        $defaults = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'active',
            'role' => 'user',
        ];

        return array_merge($defaults, $attributes);
    }

    /**
     * Seed database with data
     */
    protected function seed(string $table, array $data): void
    {
        $db = Loader::load('Database');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $db->query($sql, array_values($data));
    }

    /**
     * Truncate table
     */
    protected function truncateTable(string $table): void
    {
        $db = Loader::load('Database');
        $db->query("TRUNCATE TABLE $table");
    }
}
