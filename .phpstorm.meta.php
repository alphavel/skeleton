<?php

namespace PHPSTORM_META {

    // Facades - Global classes
    override(\Cache::class, map([
        '' => \Alphavel\Cache\Cache::class,
    ]));
    
    override(\DB::class, map([
        '' => \Alphavel\Database\Database::class,
    ]));
    
    override(\Event::class, map([
        '' => \Alphavel\Events\EventDispatcher::class,
    ]));
    
    override(\Log::class, map([
        '' => \Alphavel\Logging\Logger::class,
    ]));

    // app() helper
    override(\app(0), map([
        'cache' => \Alphavel\Cache\Cache::class,
        'db' => \Alphavel\Database\Database::class,
        'events' => \Alphavel\Events\EventDispatcher::class,
        'logger' => \Alphavel\Logging\Logger::class,
        'config' => \Alphavel\Framework\Config::class,
        'container' => \Alphavel\Framework\Container::class,
        'router' => \Alphavel\Framework\Router::class,
        'request' => \Alphavel\Framework\Request::class,
        'response' => \Alphavel\Framework\Response::class,
    ]));
}
