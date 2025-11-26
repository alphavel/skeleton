<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test basic arithmetic.
     */
    public function test_basic_arithmetic(): void
    {
        $result = 2 + 2;
        
        $this->assertEquals(4, $result);
        $this->assertIsInt($result);
    }
}
