# alphavel - True Extensibility

## Zero-Modification Plugin System

O alphavel agora suporta **extensibilidade total** sem necessidade de modificar código do core ou arquivos de configuração.

## Como Funciona

### 1. Auto-Discovery via Composer

O sistema lê `vendor/composer/installed.json` e descobre automaticamente todos os pacotes que possuem:

```json
{
    "extra": {
        "alphavel": {
            "providers": [
                "Vendor\\Package\\ServiceProvider"
            ]
        }
    }
}
```

### 2. Cache Inteligente

- **Primeira leitura**: Parse do `installed.json` (~0.5ms)
- **Leituras subsequentes**: Cache PHP (~0.001ms)
- **Invalidação**: Automática quando `composer.json` é atualizado

### 3. Zero Overhead

```
Auto-discovery com cache:   0.001ms (cached)
Auto-discovery sem cache:    0.5ms (first time)
class_exists() x6:           0.5-1ms (every request)
```

## Criando um Plugin

### Estrutura Mínima

```
packages/seu-plugin/
├── composer.json          # Metadados + auto-discovery
├── src/
│   ├── SeuPlugin.php      # Funcionalidade principal
│   └── ServiceProvider.php # Registrador
└── README.md
```

### composer.json

```json
{
    "name": "vendor/alphavel-plugin",
    "description": "Meu plugin para alphavel",
    "require": {
        "php": "^8.1",
        "alphavel/core": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\Plugin\\": "src/"
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Vendor\\Plugin\\ServiceProvider"
            ]
        }
    }
}
```

### ServiceProvider.php

```php
<?php

namespace Vendor\Plugin;

use Alphavel\Framework\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('plugin', function ($app) {
            return new SeuPlugin($app->config('plugin'));
        });
    }

    public function boot(): void
    {
        // Lógica de inicialização (opcional)
    }
}
```

## Workflow de Instalação

```bash
# Usuário instala seu plugin
composer require vendor/alphavel-plugin

# Composer atualiza vendor/composer/installed.json
# alphavel detecta automaticamente o plugin no próximo boot
# Cache é invalidado e regenerado

# Plugin está funcionando!
```

## Nenhuma Edição Necessária

✅ **NÃO** precisa editar `config/app.php`  
✅ **NÃO** precisa editar `bootstrap/app.php`  
✅ **NÃO** precisa editar código do core  
✅ **NÃO** precisa rodar comandos adicionais  

## Performance

### Comparação de Abordagens

| Abordagem | Primeira Requisição | Cache Hit | Overhead |
|-----------|-------------------|-----------|----------|
| **Auto-discovery (nova)** | 0.5ms | 0.001ms | ✅ Mínimo |
| class_exists() x6 | 0.5-1ms | 0.5-1ms | ❌ Sempre |
| Config explícito | 0.001ms | 0.001ms | ✅ Zero |

### Benchmarks Reais

```
Core only:              520k req/s (0.3MB)
Core + Database:        410k req/s (2MB)
Core + 6 plugins:       385k req/s (4MB)
```

## Override Manual (Opcional)

Se você ainda quiser controle explícito:

```php
// config/app.php
return [
    'providers' => [
        \Alphavel\Database\DatabaseServiceProvider::class,
        \Vendor\Plugin\ServiceProvider::class,
    ],
];
```

Plugins em `app.providers` são registrados **após** os descobertos automaticamente.

## Plugins Oficiais

Todos suportam auto-discovery:

- ✅ `alphavel/database` - QueryBuilder, Models, Migrations
- ✅ `alphavel/cache` - Swoole\Table shared memory cache
- ✅ `alphavel/validation` - 15+ validation rules
- ✅ `alphavel/events` - Event dispatcher pub/sub
- ✅ `alphavel/logging` - File-based logger
- ✅ `alphavel/support` - Collection utilities

## Exemplo Completo: Plugin de Email

### 1. Criar Estrutura

```bash
mkdir -p packages/email/src
```

### 2. composer.json

```json
{
    "name": "alphavel/email",
    "description": "Email plugin for alphavel",
    "require": {
        "php": "^8.1",
        "alphavel/core": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Email\\": "src/"
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Email\\EmailServiceProvider"
            ]
        }
    }
}
```

### 3. src/Mailer.php

```php
<?php

namespace Alphavel\Email;

class Mailer
{
    public function __construct(
        private array $config
    ) {}

    public function send(string $to, string $subject, string $body): bool
    {
        // Implementação
        return mail($to, $subject, $body);
    }
}
```

### 4. src/EmailServiceProvider.php

```php
<?php

namespace Alphavel\Email;

use Alphavel\Framework\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('mailer', function ($app) {
            return new Mailer($app->config('mail'));
        });
    }
}
```

### 5. Usar

```bash
composer require alphavel/email
```

```php
// app/Controllers/ContactController.php
$mailer = app('mailer');
$mailer->send('user@example.com', 'Hello', 'World');
```

## Cache Manual

Se precisar limpar o cache:

```bash
php clear-cache.php
# ou
rm storage/cache/providers.php
```

## Vantagens

✅ **Verdadeira extensibilidade** - Zero modificações no core  
✅ **Performance** - Cache inteligente com invalidação automática  
✅ **Developer Experience** - `composer require` e funciona  
✅ **Padrão Laravel** - Mesmo sistema usado pelo Laravel  
✅ **Type-safe** - IntelliSense completo  
✅ **Testável** - Plugins isolados e mockáveis  

## Limitações

⚠️ **Ordem de carregamento**: Plugins são carregados em ordem alfabética. Se precisar ordem específica, use `app.providers` no config.

⚠️ **Conflitos**: Dois plugins não podem registrar o mesmo serviço. O primeiro vence.

⚠️ **Namespace**: Plugins devem usar PSR-4 autoloading correto.

## Conclusão

O alphavel agora é **100% extensível** mantendo **máxima performance**. Desenvolvedores podem criar e distribuir plugins sem nunca tocar no código do framework.

```bash
composer require awesome/plugin
# Pronto! 🚀
```
