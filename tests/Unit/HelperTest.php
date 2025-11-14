<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Helper Functions Test
 *
 * Tests global helper functions
 */
class HelperTest extends TestCase
{
    public function test_now_helper(): void
    {
        $now = now();

        $this->assertIsString($now);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $now);
    }

    public function test_today_helper(): void
    {
        $today = today();

        $this->assertIsString($today);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $today);
    }

    public function test_collect_helper(): void
    {
        $collection = collect([1, 2, 3]);

        $this->assertInstanceOf(\Alphavel\Support\Collection::class, $collection);
    }

    public function test_array_get_helper(): void
    {
        $array = ['user' => ['name' => 'John', 'email' => 'john@example.com']];

        $name = array_get($array, 'user.name');
        $email = array_get($array, 'user.email');
        $missing = array_get($array, 'user.age', 25);

        $this->assertEquals('John', $name);
        $this->assertEquals('john@example.com', $email);
        $this->assertEquals(25, $missing);
    }

    public function test_array_set_helper(): void
    {
        $array = [];

        array_set($array, 'user.name', 'John');
        array_set($array, 'user.email', 'john@example.com');

        $this->assertEquals('John', $array['user']['name']);
        $this->assertEquals('john@example.com', $array['user']['email']);
    }

    public function test_array_has_helper(): void
    {
        $array = ['user' => ['name' => 'John']];

        $this->assertTrue(array_has($array, 'user.name'));
        $this->assertFalse(array_has($array, 'user.email'));
    }

    public function test_str_contains_helper(): void
    {
        $this->assertTrue(str_contains('Hello World', 'World'));
        $this->assertFalse(str_contains('Hello World', 'Goodbye'));
    }

    public function test_str_starts_with_helper(): void
    {
        $this->assertTrue(str_starts_with('Hello World', 'Hello'));
        $this->assertFalse(str_starts_with('Hello World', 'World'));
    }

    public function test_str_ends_with_helper(): void
    {
        $this->assertTrue(str_ends_with('Hello World', 'World'));
        $this->assertFalse(str_ends_with('Hello World', 'Hello'));
    }

    public function test_str_slug_helper(): void
    {
        $slug = str_slug('My Blog Post Title');

        $this->assertEquals('my-blog-post-title', $slug);
    }

    public function test_value_helper(): void
    {
        $value = value('static');
        $callable = value(fn () => 'computed');

        $this->assertEquals('static', $value);
        $this->assertEquals('computed', $callable);
    }
}
