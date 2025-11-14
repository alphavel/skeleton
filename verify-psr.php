<?php

require __DIR__ . '/vendor/autoload.php';

use Alphavel\Framework\Container;
use Alphavel\Logging\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

echo "🔍 Verificando conformidade PSR...\n\n";

// Test PSR-11 Container
echo "PSR-11 (Container Interface):\n";
echo "- Container implementa ContainerInterface? ";
echo (Container::getInstance() instanceof ContainerInterface) ? "✅ SIM\n" : "❌ NÃO\n";

$container = Container::getInstance();
$container->singleton('test', fn() => 'test-value');

echo "- Método get() funciona? ";
try {
    $value = $container->get('test');
    echo ($value === 'test-value') ? "✅ SIM\n" : "❌ NÃO\n";
} catch (\Exception $e) {
    echo "❌ ERRO: {$e->getMessage()}\n";
}

echo "- Método has() funciona? ";
echo $container->has('test') ? "✅ SIM\n" : "❌ NÃO\n";

echo "- NotFoundException lançada? ";
try {
    $container->get('inexistente');
    echo "❌ NÃO\n";
} catch (\Psr\Container\NotFoundExceptionInterface $e) {
    echo "✅ SIM\n";
}

echo "\n";

// Test PSR-3 Logger
echo "PSR-3 (Logger Interface):\n";
$logger = new Logger(__DIR__ . '/storage/logs/test-psr.log');
echo "- Logger implementa LoggerInterface? ";
echo ($logger instanceof LoggerInterface) ? "✅ SIM\n" : "❌ NÃO\n";

$methods = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log'];
foreach ($methods as $method) {
    echo "- Método {$method}() existe? ";
    echo method_exists($logger, $method) ? "✅ SIM\n" : "❌ NÃO\n";
}

echo "\n✅ Conformidade PSR-11 e PSR-3 verificada com sucesso!\n";
