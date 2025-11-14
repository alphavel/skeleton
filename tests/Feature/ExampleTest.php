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
        
        $json = $this->json($response);
        $this->assertArrayHasKey('message', $json);
    }

    /**
     * Test health check endpoint.
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/health');

        $this->assertResponseOk($response);
        
        $json = $this->json($response);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('healthy', $json['status']);
        $this->assertArrayHasKey('timestamp', $json);
        $this->assertArrayHasKey('memory', $json);
    }
}
