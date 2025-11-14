<?php

/**
 * PHPUnit Bootstrap
 *
 * Carrega o autoloader e configurações necessárias para os testes
 */

// Autoloader do Composer
require_once __DIR__.'/../vendor/autoload.php';

// Carrega helpers
require_once __DIR__.'/../app/helpers.php';

// Carrega Loader para componentes
require_once __DIR__.'/../app/Core/Loader.php';

// Define variáveis de ambiente para testes
$_ENV['APP_ENV'] = 'testing';
$_ENV['DB_HOST'] = getenv('DB_HOST') ?: 'localhost';
$_ENV['DB_NAME'] = getenv('DB_NAME') ?: 'test_db';
$_ENV['DB_USER'] = getenv('DB_USER') ?: 'root';
$_ENV['DB_PASS'] = getenv('DB_PASS') ?: 'password';
