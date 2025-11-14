<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $this->assertResponseOk($response);
        $this->assertArrayHasKey('message', $response);
    }

    /**
     * Test health check endpoint.
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/health');

        $this->assertResponseOk($response);
        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('healthy', $response['status']);
    }
}
