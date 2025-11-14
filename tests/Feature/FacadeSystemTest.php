<?php

namespace Tests\Feature;

use Tests\TestCase;

class FacadeSystemTest extends TestCase
{
    /**
     * Test facade cache generation
     */
    public function testFacadeCacheGeneration(): void
    {
        $cacheFile = __DIR__ . '/../../storage/framework/facades.php';

        // Clear existing cache
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Bootstrap application
        $app = \Alphavel\Framework\Application::getInstance();
        $app->loadConfig(__DIR__ . '/../../config/app-test.php');

        // Register providers with facades
        $app->register(\Alphavel\Database\DatabaseServiceProvider::class);
        $app->register(\Alphavel\Cache\CacheServiceProvider::class);
        $app->register(\Alphavel\Logging\LoggingServiceProvider::class);
        $app->register(\Alphavel\Events\EventServiceProvider::class);

        $app->boot();

        // Assert cache file was created
        $this->assertTrue(file_exists($cacheFile), 'Facade cache file should be generated');
        $this->assertGreaterThan(0, filesize($cacheFile), 'Facade cache file should not be empty');

        // Assert facades exist
        $this->assertTrue(class_exists('Cache'), 'Cache facade should exist');
        $this->assertTrue(class_exists('DB'), 'DB facade should exist');
        $this->assertTrue(class_exists('Log'), 'Log facade should exist');
        $this->assertTrue(class_exists('Event'), 'Event facade should exist');
    }

    /**
     * Test facade accessor methods
     */
    public function testFacadeAccessors(): void
    {
        // Require cache file if exists
        $cacheFile = __DIR__ . '/../../storage/framework/facades.php';

        if (! file_exists($cacheFile)) {
            $this->markTestSkipped('Facade cache not generated yet');
        }

        require_once $cacheFile;

        // Test facade accessors
        $this->assertEquals('cache', \Cache::getFacadeAccessor());
        $this->assertEquals('db', \DB::getFacadeAccessor());
        $this->assertEquals('logger', \Log::getFacadeAccessor());
        $this->assertEquals('events', \Event::getFacadeAccessor());
    }
}
