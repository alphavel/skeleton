<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Controllers\HomeController;
use Alphavel\Framework\Response;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $controller = new HomeController();
        $response = $controller->index();

        $this->assertInstanceOf(Response::class, $response);
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
    }
}
