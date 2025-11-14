<?php

/**
 * PHPUnit Bootstrap
 */

// Load Composer autoloader
require_once __DIR__.'/../vendor/autoload.php';

// Set environment to testing
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_DEBUG'] = true;
