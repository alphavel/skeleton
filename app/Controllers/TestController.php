<?php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Response;
// Cache: usar app("cache") em vez de facade;
// DB: usar app("db") em vez de facade;
// Event: usar app("events") em vez de facade;
// Log: usar app("logger") em vez de facade;

/**
 * TestController
 *
 * Test endpoints for framework features
 */
class TestController extends Controller
{
    /**
     * Test database connection
     */
    public function testDb(): Response
    {
        $result = DB::query('SELECT 1 as test, NOW() as timestamp');

        return Response::success([
            'message' => 'Database connection OK',
            'result' => $result,
        ]);
    }

    /**
     * Test cache functionality
     */
    public function testCache(): Response
    {
        // Set cache
        Cache::set('test_key', 'Hello from cache!', 60);

        // Get cache
        $value = Cache::get('test_key');

        // Test remember
        $remembered = Cache::remember('expensive_query', 300, function () {
            return [
                'computed_at' => now(),
                'random' => rand(1000, 9999),
            ];
        });

        return Response::success([
            'cache_value' => $value,
            'remembered' => $remembered,
            'message' => 'Cache working!',
        ]);
    }

    /**
     * Test collection operations
     */
    public function testCollection(): Response
    {
        $data = [
            ['name' => 'John', 'age' => 25, 'city' => 'NY'],
            ['name' => 'Jane', 'age' => 30, 'city' => 'LA'],
            ['name' => 'Bob', 'age' => 35, 'city' => 'NY'],
            ['name' => 'Alice', 'age' => 28, 'city' => 'LA'],
        ];

        // Collection operations
        $result = collect($data)
            ->where('age', '>=', 28)
            ->sortBy('name')
            ->pluck('name')
            ->values();

        $grouped = collect($data)->groupBy('city');

        $stats = [
            'avg_age' => collect($data)->avg('age'),
            'total' => collect($data)->count(),
        ];

        return Response::success([
            'filtered' => $result->toArray(),
            'grouped' => $grouped->toArray(),
            'stats' => $stats,
        ]);
    }

    /**
     * Test facades
     */
    public function testFacades(): Response
    {
        // Test DB facade
        $dbTest = DB::query("SELECT 'DB Facade works!' as message");

        // Test Cache facade
        Cache::set('facade_test', 'Cache Facade works!', 60);
        $cacheTest = Cache::get('facade_test');

        // Test Log facade
        Log::info('Testing facades', ['user' => 'test']);

        // Test Event facade
        Event::listen('test.event', function ($data) {
            Log::info('Event triggered', $data);
        });
        Event::dispatch('test.event', ['message' => 'Hello from event!']);

        return Response::success([
            'db' => $dbTest[0]['message'] ?? 'error',
            'cache' => $cacheTest,
            'log' => 'Check storage/logs/app.log',
            'event' => 'Event dispatched',
        ]);
    }
}
