<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A simple passing test.
     */
    public function test_basic_assertion(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test basic math.
     */
    public function test_basic_math(): void
    {
        $this->assertEquals(4, 2 + 2);
    }
}
