<?php

namespace Tests\Unit;

use Alphavel\Framework\Collection;
use Tests\TestCase;

/**
 * Collection Test
 *
 * Tests the Collection class methods
 */
class CollectionTest extends TestCase
{
    public function test_collection_creation(): void
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(5, $collection->count());
    }

    public function test_collection_where(): void
    {
        $data = [
            ['name' => 'John', 'age' => 25],
            ['name' => 'Jane', 'age' => 30],
            ['name' => 'Bob', 'age' => 35],
        ];

        $result = collect($data)->where('age', '>', 28);

        $this->assertEquals(2, $result->count());
    }

    public function test_collection_map(): void
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $result = $collection->map(fn ($n) => $n * 2);

        $this->assertEquals([2, 4, 6, 8, 10], $result->toArray());
    }

    public function test_collection_filter(): void
    {
        $collection = collect([1, 2, 3, 4, 5, 6]);

        $result = $collection->filter(fn ($n) => $n % 2 === 0);

        $this->assertEquals(3, $result->count());
    }

    public function test_collection_pluck(): void
    {
        $data = [
            ['name' => 'John', 'age' => 25],
            ['name' => 'Jane', 'age' => 30],
        ];

        $names = collect($data)->pluck('name');

        $this->assertEquals(['John', 'Jane'], $names->toArray());
    }

    public function test_collection_sum(): void
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $sum = $collection->sum();

        $this->assertEquals(15, $sum);
    }

    public function test_collection_avg(): void
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $avg = $collection->avg();

        $this->assertEquals(3, $avg);
    }

    public function test_collection_group_by(): void
    {
        $data = [
            ['name' => 'John', 'city' => 'NY'],
            ['name' => 'Jane', 'city' => 'LA'],
            ['name' => 'Bob', 'city' => 'NY'],
        ];

        $grouped = collect($data)->groupBy('city');

        $this->assertCount(2, $grouped);
        $this->assertArrayHasKey('NY', $grouped);
        $this->assertArrayHasKey('LA', $grouped);
    }

    public function test_collection_sort_by(): void
    {
        $data = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
            ['name' => 'Bob', 'age' => 35],
        ];

        $sorted = collect($data)->sortBy('age')->values();

        $this->assertEquals('Jane', $sorted[0]['name']);
        $this->assertEquals('Bob', $sorted[2]['name']);
    }

    public function test_collection_chaining(): void
    {
        $data = [
            ['name' => 'John', 'age' => 25, 'active' => true],
            ['name' => 'Jane', 'age' => 30, 'active' => true],
            ['name' => 'Bob', 'age' => 35, 'active' => false],
        ];

        $result = collect($data)
            ->where('active', true)
            ->sortBy('age')
            ->pluck('name')
            ->toArray();

        $this->assertEquals(['John', 'Jane'], $result);
    }
}
