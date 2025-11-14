<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * API Test
 *
 * Tests API endpoints
 */
class ApiTest extends TestCase
{
    public function test_home_endpoint(): void
    {
        $response = $this->getJson('/');

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('success', $response['status']);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_api_data_endpoint(): void
    {
        $response = $this->getJson('/api/data');

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_users_list_endpoint(): void
    {
        $response = $this->getJson('/api/users');

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_cache_test_endpoint(): void
    {
        $response = $this->getJson('/test/cache');

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_collection_test_endpoint(): void
    {
        $response = $this->getJson('/test/collection');

        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_facades_test_endpoint(): void
    {
        $response = $this->getJson('/test/facades');

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('success', $response['status']);
    }
}
