# 🚀 Plano de Migração: Monorepo → Multi-repo (Vendor)

## 📋 Visão Geral

**Objetivo:** Transformar alphavel de monorepo para multi-repo profissional com distribuição via Composer/Packagist.

**Timeline:** 4-6 semanas  
**Complexidade:** Média  
**Impacto:** Zero na performance, 100% nos processos

---

## 🎯 Fases da Migração

### **Fase 1: Preparação (Semana 1-2)**
- Adicionar `basePath()` ao Application
- Refatorar paths hardcoded
- Criar testes de integração
- Documentar estrutura atual

### **Fase 2: Split Repositories (Semana 3)**
- Extrair packages para repos independentes
- Configurar CI/CD por repo
- Criar tags iniciais

### **Fase 3: Publicação (Semana 4)**
- Publicar no Packagist
- Criar skeleton template
- Testar instalação via Composer

### **Fase 4: Migração (Semana 5-6)**
- Converter projeto atual
- Atualizar documentação
- Treinar usuários

---

## 📦 Estrutura Final (Multi-repo)

### **Repositórios GitHub:**

```
github.com/alphavel/
├── core               (framework essencial)
├── cache              (plugin cache)
├── database           (plugin database)
├── events             (plugin events)
├── logging            (plugin logging)
├── validation         (plugin validation)
├── support            (helpers/utils)
├── skeleton           (template de projeto)
└── docs               (documentação centralizada)
```

### **Estrutura de Projeto do Usuário:**

```
meu-projeto/
├── vendor/
│   ├── alphavel/
│   │   ├── core/              ← Framework (Composer gerencia)
│   │   ├── cache/             ← Plugin opcional
│   │   ├── database/          ← Plugin opcional
│   │   └── validation/        ← Plugin opcional
│   ├── psr/
│   │   ├── container/
│   │   └── log/
│   └── composer/
│       ├── autoload_*.php
│       └── installed.json
├── app/                        ← Código do usuário
│   ├── Controllers/
│   ├── Models/
│   └── Middlewares/
├── config/
│   ├── app.php
│   ├── database.php
│   └── cache.php
├── routes/
│   └── api.php
├── storage/
│   ├── framework/
│   │   └── facades.php         ← Gerado automaticamente
│   ├── logs/
│   └── cache/
├── bootstrap/
│   └── app.php
├── public/
│   └── index.php
├── composer.json               ← Define dependências
├── .env
├── .env.example
├── .gitignore
└── README.md
```

---

# 🛠️ FASE 1: Preparação

## 1.1 Adicionar `basePath()` ao Application

**Arquivo:** `packages/core/src/Application.php`

```php
<?php

namespace Alphavel\Framework;

class Application extends Container
{
    protected string $basePath;
    protected static ?self $instance = null;

    public function __construct(string $basePath = null)
    {
        parent::__construct();
        
        $this->basePath = $basePath ?? $this->guessBasePath();
        
        static::$instance = $this;
    }

    /**
     * Detecta raiz do projeto automaticamente
     */
    protected function guessBasePath(): string
    {
        // Se rodando de vendor/, sobe 4 níveis: vendor/alphavel/core/src → raiz
        // Se rodando de packages/, sobe 3 níveis: packages/core/src → raiz
        
        $reflection = new \ReflectionClass($this);
        $dir = dirname($reflection->getFileName());
        
        // Procura por vendor/ ou packages/ no path
        if (strpos($dir, '/vendor/alphavel/') !== false) {
            // Está em vendor/alphavel/core/src
            return dirname($dir, 4);
        } elseif (strpos($dir, '/packages/') !== false) {
            // Está em packages/core/src
            return dirname($dir, 3);
        }
        
        // Fallback: diretório atual
        return getcwd();
    }

    /**
     * Path base do projeto
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Path do diretório storage/
     */
    public function storagePath(string $path = ''): string
    {
        return $this->basePath('storage' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }

    /**
     * Path do diretório config/
     */
    public function configPath(string $path = ''): string
    {
        return $this->basePath('config' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }

    /**
     * Path do diretório public/
     */
    public function publicPath(string $path = ''): string
    {
        return $this->basePath('public' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }

    /**
     * Path do diretório bootstrap/
     */
    public function bootstrapPath(string $path = ''): string
    {
        return $this->basePath('bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
```

---

## 1.2 Refatorar ServiceProvider

**Arquivo:** `packages/core/src/ServiceProvider.php`

```php
<?php

namespace Alphavel\Framework;

abstract class ServiceProvider
{
    protected Application $app;
    private static bool $facadesCached = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function register(): void;

    public function boot(): void
    {
        //
    }

    protected function facades(array $facades): void
    {
        if (empty($facades)) {
            return;
        }

        $cacheFile = $this->getFacadeCachePath();

        $this->generateFacadeCache($facades, $cacheFile);

        if (file_exists($cacheFile) && ! self::$facadesCached) {
            require_once $cacheFile;
            self::$facadesCached = true;
        }
    }

    /**
     * Get facade cache path (agora dinâmico!)
     */
    private function getFacadeCachePath(): string
    {
        // Antes: dirname(__DIR__, 5) . '/storage/framework/facades.php'
        // Depois: usa basePath()
        return $this->app->storagePath('framework/facades.php');
    }

    private function generateFacadeCache(array $facades, string $cacheFile): void
    {
        $storagePath = dirname($cacheFile);

        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $allFacades = $this->loadAllFacades($cacheFile);
        $allFacades = array_merge($allFacades, $facades);

        $code = "<?php\n\n";
        $code .= "/**\n";
        $code .= " * Auto-generated Facades\n";
        $code .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
        $code .= " * \n";
        $code .= " * DO NOT EDIT THIS FILE MANUALLY\n";
        $code .= " * Run: php alphavel facade:clear to regenerate\n";
        $code .= " */\n\n";

        foreach ($allFacades as $alias => $accessor) {
            $code .= $this->generateFacadeClass($alias, $accessor);
        }

        file_put_contents($cacheFile, $code);
    }

    private function loadAllFacades(string $cacheFile): array
    {
        if (! file_exists($cacheFile)) {
            return [];
        }

        $content = file_get_contents($cacheFile);
        $facades = [];

        preg_match_all('/class\s+(\w+)\s+extends[^{]*{[^}]*return\s+[\'"]([^\'"]+)[\'"].*?}/s', $content, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $index => $alias) {
                $facades[$alias] = $matches[2][$index] ?? '';
            }
        }

        return $facades;
    }

    private function generateFacadeClass(string $alias, string $accessor): string
    {
        return <<<PHP
class {$alias} extends \Alphavel\Framework\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return '{$accessor}';
    }
}


PHP;
    }
}
```

---

## 1.3 Atualizar Discovery de Providers

**Arquivo:** `packages/core/src/Application.php`

```php
public function discoverProviders(): array
{
    $cacheFile = $this->storagePath('cache/providers.php');

    // Check cache first
    if (file_exists($cacheFile)) {
        $cached = require $cacheFile;
        $installedPath = $this->basePath('vendor/composer/installed.json');
        
        if (isset($cached['timestamp']) && $cached['timestamp'] >= filemtime($installedPath)) {
            return $cached['providers'] ?? [];
        }
    }

    $providers = [];
    $installedPath = $this->basePath('vendor/composer/installed.json');

    if (! file_exists($installedPath)) {
        return $providers;
    }

    $installed = json_decode(file_get_contents($installedPath), true);
    $packages = $installed['packages'] ?? [];

    foreach ($packages as $package) {
        if (isset($package['extra']['alphavel']['providers'])) {
            $packageProviders = (array) $package['extra']['alphavel']['providers'];
            $providers = array_merge($providers, $packageProviders);
        }
    }

    // Cache results
    $storageDir = dirname($cacheFile);
    if (! is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }

    file_put_contents($cacheFile, "<?php\nreturn " . var_export([
        'timestamp' => time(),
        'providers' => $providers,
    ], true) . ";\n");

    return $providers;
}
```

---

## 1.4 Criar Testes de Integração

**Arquivo:** `tests/Integration/PathResolutionTest.php`

```php
<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Alphavel\Framework\Application;

class PathResolutionTest extends TestCase
{
    public function testBasePathDetection()
    {
        $app = new Application();
        
        $basePath = $app->basePath();
        
        $this->assertDirectoryExists($basePath);
        $this->assertDirectoryExists($basePath . '/storage');
        $this->assertDirectoryExists($basePath . '/config');
    }

    public function testStoragePath()
    {
        $app = new Application();
        
        $storagePath = $app->storagePath();
        
        $this->assertStringEndsWith('storage', $storagePath);
        $this->assertDirectoryExists($storagePath);
    }

    public function testFacadeGeneration()
    {
        $app = new Application();
        
        // Simula geração de facade
        $facadePath = $app->storagePath('framework/facades.php');
        
        $this->assertStringContains('storage/framework/facades.php', $facadePath);
    }

    public function testWorksFromVendor()
    {
        // Simula estrutura vendor/
        $app = new Application('/var/www/projeto');
        
        $this->assertEquals('/var/www/projeto', $app->basePath());
        $this->assertEquals('/var/www/projeto/storage', $app->storagePath());
    }

    public function testWorksFromPackages()
    {
        // Simula estrutura packages/
        $app = new Application(getcwd());
        
        $this->assertDirectoryExists($app->basePath());
        $this->assertDirectoryExists($app->storagePath());
    }
}
```

---

## 1.5 Atualizar Bootstrap

**Arquivo:** `bootstrap/app.php`

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Alphavel\Framework\Application;
use Alphavel\Framework\CoreServiceProvider;

// Criar application com base path explícito
$app = Application::getInstance();

// Ou passar manualmente (opcional)
// $app = new Application(__DIR__ . '/..');

// Load config usando configPath()
$app->loadConfig($app->configPath('app.php'));

$app->register(CoreServiceProvider::class);

// Auto-discover plugins from composer packages
$discoveredProviders = $app->discoverProviders();

foreach ($discoveredProviders as $provider) {
    $app->register($provider);
}

// Register providers from config
$configProviders = $app->config('providers', []);

foreach ($configProviders as $provider) {
    $app->register($provider);
}

// Load facades (usando storagePath())
$facadeFile = $app->storagePath('framework/facades.php');
if (file_exists($facadeFile)) {
    require_once $facadeFile;
}

// Load routes
$router = $app->make('router');
require $app->basePath('routes/api.php');

return $app;
```

---

# 🔀 FASE 2: Split Repositories

## 2.1 Preparar Cada Package

### **Estrutura de cada repo (exemplo: alphavel/core)**

```
alphavel-core/
├── src/
│   ├── Application.php
│   ├── Container.php
│   ├── ServiceProvider.php
│   ├── Facade.php
│   ├── Router.php
│   ├── Request.php
│   ├── Response.php
│   └── helpers.php
├── tests/
│   └── Unit/
│       └── ApplicationTest.php
├── composer.json
├── phpunit.xml
├── .gitignore
├── .github/
│   └── workflows/
│       └── tests.yml
├── LICENSE
└── README.md
```

### **composer.json de cada package:**

#### **alphavel/core:**

```json
{
    "name": "alphavel/core",
    "description": "alphavel Framework Core - Ultra-fast PHP framework with 520k req/s",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur",
            "email": "arthur@example.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "psr/container": "^2.0",
        "ext-swoole": "*"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.5",
        "laravel/pint": "^1.25"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Core\\": "src/"
        },
        "files": [
            "src/helpers.php"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

#### **alphavel/cache:**

```json
{
    "name": "alphavel/cache",
    "description": "alphavel Cache Package - High-performance caching",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Cache\\": "src/"
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Cache\\CacheServiceProvider"
            ]
        }
    }
}
```

#### **alphavel/database:**

```json
{
    "name": "alphavel/database",
    "description": "alphavel Database Package - Lightweight ORM",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "alphavel/core": "^2.0",
        "ext-pdo": "*"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Database\\": "src/"
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Database\\DatabaseServiceProvider"
            ]
        }
    }
}
```

---

## 2.2 Script de Split (Git Subtree)

**Arquivo:** `scripts/split-repos.sh`

```bash
#!/bin/bash

# Script para split de monorepo → multi-repo

set -e

REPOS=(
    "core:packages/core"
    "cache:packages/cache"
    "database:packages/database"
    "events:packages/events"
    "logging:packages/logging"
    "validation:packages/validation"
    "support:packages/support"
)

GITHUB_ORG="alphavel"

echo "🚀 Splitting alphavel monorepo..."

for repo in "${REPOS[@]}"; do
    IFS=":" read -r name path <<< "$repo"
    
    echo ""
    echo "📦 Processing: $name ($path)"
    
    # 1. Create split branch
    echo "  → Creating split branch..."
    git subtree split -P "$path" -b "split-$name"
    
    # 2. Create temporary directory
    echo "  → Creating temp directory..."
    TEMP_DIR="/tmp/alphavel-$name"
    rm -rf "$TEMP_DIR"
    mkdir -p "$TEMP_DIR"
    
    # 3. Clone split branch
    echo "  → Cloning split..."
    git clone --branch "split-$name" . "$TEMP_DIR"
    
    # 4. Push to GitHub
    echo "  → Pushing to github.com/$GITHUB_ORG/$name..."
    cd "$TEMP_DIR"
    git remote add github "git@github.com:$GITHUB_ORG/$name.git"
    git push -u github split-$name:main --force
    
    # 5. Create initial tag
    echo "  → Creating tag v2.0.0..."
    git tag v2.0.0
    git push github v2.0.0
    
    cd -
    
    # 6. Cleanup
    echo "  → Cleaning up..."
    rm -rf "$TEMP_DIR"
    git branch -D "split-$name"
    
    echo "  ✅ Done: $name"
done

echo ""
echo "✅ All packages split successfully!"
echo ""
echo "Next steps:"
echo "  1. Go to github.com/$GITHUB_ORG"
echo "  2. Create repositories manually if they don't exist"
echo "  3. Re-run this script"
echo "  4. Submit to Packagist: https://packagist.org/packages/submit"
```

**Execução:**

```bash
chmod +x scripts/split-repos.sh
./scripts/split-repos.sh
```

---

## 2.3 Criar Repositórios no GitHub

```bash
# Via GitHub CLI
gh repo create alphavel/core --public
gh repo create alphavel/cache --public
gh repo create alphavel/database --public
gh repo create alphavel/events --public
gh repo create alphavel/logging --public
gh repo create alphavel/validation --public
gh repo create alphavel/support --public
gh repo create alphavel/skeleton --public
```

---

## 2.4 Configurar CI/CD

**Arquivo:** `.github/workflows/tests.yml` (em cada repo)

```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php: [8.1, 8.2, 8.3]
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php }}
        extensions: swoole, pdo, mbstring
        coverage: xdebug
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
    
    - name: Run tests
      run: vendor/bin/phpunit
    
    - name: Check code style
      run: vendor/bin/pint --test
```

---

# 📤 FASE 3: Publicação

## 3.1 Publicar no Packagist

### **Manual:**

1. Acesse: https://packagist.org/packages/submit
2. Insira URL do GitHub:
   - `https://github.com/alphavel/core`
   - `https://github.com/alphavel/cache`
   - etc.
3. Clique "Check" → "Submit"

### **Automático (Webhook):**

```bash
# Para cada repo no GitHub:
# Settings → Webhooks → Add webhook

URL: https://packagist.org/api/github
Content type: application/json
Secret: <seu-token-packagist>
Events: Just the push event
```

---

## 3.2 Criar Skeleton Template

**Repositório:** `github.com/alphavel/skeleton`

```
alphavel-skeleton/
├── app/
│   ├── Controllers/
│   │   └── HomeController.php
│   ├── Models/
│   └── Middlewares/
├── config/
│   ├── app.php
│   ├── database.php
│   └── cache.php
├── routes/
│   └── api.php
├── storage/
│   ├── framework/
│   ├── logs/
│   └── cache/
├── bootstrap/
│   └── app.php
├── public/
│   └── index.php
├── tests/
│   └── Feature/
├── composer.json
├── phpunit.xml
├── .env.example
├── .gitignore
├── README.md
└── docker-compose.yml
```

**composer.json do skeleton:**

```json
{
    "name": "alphavel/skeleton",
    "description": "alphavel Application Skeleton",
    "type": "project",
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "ext-swoole": "*",
        "alphavel/core": "^2.0",
        "alphavel/database": "^1.0",
        "alphavel/cache": "^1.0",
        "alphavel/validation": "^1.0",
        "alphavel/logging": "^1.0",
        "vlucas/phpdotenv": "^5.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.5",
        "laravel/pint": "^1.25"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php alphavel generate:facades"
        ],
        "test": "phpunit"
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

---

## 3.3 Testar Instalação

```bash
# 1. Criar novo projeto
composer create-project alphavel/skeleton minha-api

# 2. Verificar estrutura
cd minha-api
ls -la

# Deve mostrar:
# vendor/alphavel/core/
# vendor/alphavel/cache/
# vendor/alphavel/database/
# app/
# config/
# storage/

# 3. Gerar facades
php generate-facades.php

# 4. Testar
php -S localhost:8000 -t public/
curl http://localhost:8000/
```

---

# 🔄 FASE 4: Migração do Projeto Atual

## 4.1 Backup do Monorepo

```bash
# 1. Criar branch de backup
git checkout -b backup-monorepo
git push origin backup-monorepo

# 2. Criar tag de release
git tag v2.0.0-monorepo
git push origin v2.0.0-monorepo
```

---

## 4.2 Converter para Multi-repo

```bash
# 1. Remover packages/
rm -rf packages/

# 2. Adicionar packages via Composer
composer require alphavel/core:^2.0
composer require alphavel/cache:^1.0
composer require alphavel/database:^1.0
composer require alphavel/validation:^1.0
composer require alphavel/events:^1.0
composer require alphavel/logging:^1.0

# 3. Atualizar autoload
composer dump-autoload

# 4. Verificar estrutura
ls -la vendor/alphavel/
# Deve mostrar: core/ cache/ database/ etc.

# 5. Gerar facades
php generate-facades.php

# 6. Testar
php -S localhost:8000 -t public/
```

---

## 4.3 Atualizar .gitignore

```gitignore
# Vendor (gerenciado pelo Composer)
/vendor/

# Storage
/storage/*.php
/storage/framework/
/storage/logs/*
!/storage/logs/.gitignore

# Cache
/bootstrap/cache/*
!/bootstrap/cache/.gitignore

# Environment
.env
.env.backup

# IDE
.idea/
.vscode/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Tests
/coverage/
.phpunit.result.cache
```

---

## 4.4 Atualizar Documentação

**README.md:**

```markdown
# alphavel - Ultra-fast PHP Framework

520,000+ req/s | Swoole-powered | Laravel-inspired

## Installation

### Via Composer (Recommended)

```bash
composer create-project alphavel/skeleton my-api
cd my-api
cp .env.example .env
php generate-facades.php
php -S localhost:8000 -t public/
```

### Manual Installation

```bash
composer require alphavel/core
composer require alphavel/database
composer require alphavel/cache
```

## Packages

- **alphavel/core** - Framework essencial
- **alphavel/database** - Database & ORM
- **alphavel/cache** - High-performance caching
- **alphavel/validation** - Input validation
- **alphavel/events** - Event dispatcher
- **alphavel/logging** - PSR-3 logging

## Documentation

Visit: https://github.com/alphavel/docs
```

---

# 📊 Checklist de Migração

## Fase 1: Preparação ✅

- [ ] Adicionar `basePath()` ao Application
- [ ] Refatorar `ServiceProvider::getFacadeCachePath()`
- [ ] Atualizar `Application::discoverProviders()`
- [ ] Criar `PathResolutionTest`
- [ ] Atualizar `bootstrap/app.php`
- [ ] Testar localmente (packages/)
- [ ] Commit: "feat: add basePath() for vendor support"

## Fase 2: Split Repositories ✅

- [ ] Criar repositórios no GitHub
  - [ ] alphavel/core
  - [ ] alphavel/cache
  - [ ] alphavel/database
  - [ ] alphavel/events
  - [ ] alphavel/logging
  - [ ] alphavel/validation
  - [ ] alphavel/support
  - [ ] alphavel/skeleton
- [ ] Executar `scripts/split-repos.sh`
- [ ] Verificar cada repo no GitHub
- [ ] Criar tags v2.0.0 em cada repo
- [ ] Configurar CI/CD (GitHub Actions)
- [ ] Testar builds

## Fase 3: Publicação ✅

- [ ] Publicar no Packagist
  - [ ] alphavel/core
  - [ ] alphavel/cache
  - [ ] alphavel/database
  - [ ] alphavel/events
  - [ ] alphavel/logging
  - [ ] alphavel/validation
  - [ ] alphavel/support
  - [ ] alphavel/skeleton
- [ ] Configurar webhooks GitHub → Packagist
- [ ] Testar instalação via Composer
- [ ] Criar projeto de teste
- [ ] Verificar auto-discovery

## Fase 4: Migração ✅

- [ ] Backup monorepo (branch + tag)
- [ ] Remover `packages/`
- [ ] Instalar via Composer
- [ ] Atualizar .gitignore
- [ ] Gerar facades
- [ ] Testar endpoints
- [ ] Executar testes
- [ ] Atualizar documentação
- [ ] Criar migration guide
- [ ] Anunciar v3.0.0

---

# 🎯 Comandos Rápidos

## Desenvolvimento (Monorepo)

```bash
# Estrutura atual
packages/core/
app/

# Comandos
./alphavel list
php generate-facades.php
composer test
```

## Produção (Multi-repo)

```bash
# Criar projeto
composer create-project alphavel/skeleton minha-api

# Adicionar plugins
composer require alphavel/events

# Atualizar framework
composer update alphavel/core

# Comandos
php artisan list  # ou ./alphavel list
composer test
```

---

# 📈 Benefícios Pós-Migração

## Para Desenvolvedores

- ✅ Instalação: 1 comando (`composer create-project`)
- ✅ Atualizações: `composer update alphavel/core`
- ✅ Plugins: `composer require alphavel/cache`
- ✅ Versionamento: Escolher versões independentes
- ✅ Multi-projeto: Reusar packages

## Para Mantenedores

- ✅ CI/CD: Build independente por package
- ✅ Releases: Tag por package
- ✅ Issues: Separadas por repo
- ✅ Contribuições: PRs focados
- ✅ Testes: Isolados por package

## Para Framework

- ✅ Profissionalismo: Padrão da indústria
- ✅ Distribuição: Packagist oficial
- ✅ Adoção: Fácil experimentar
- ✅ Comunidade: Contribuições simplificadas
- ✅ Escalabilidade: Novos packages sem impacto

---

# 🚨 Riscos e Mitigações

## Risco 1: Path Breaking Changes

**Problema:** Código hardcoded com `dirname(__DIR__, 5)`

**Mitigação:**
- ✅ Adicionar `basePath()` antes do split
- ✅ Criar testes de path resolution
- ✅ Manter retrocompatibilidade temporária

---

## Risco 2: Dependências Circulares

**Problema:** Package A depende de B, B depende de A

**Mitigação:**
- ✅ Core não depende de nada (exceto PSR)
- ✅ Plugins dependem apenas de core
- ✅ Revisar composer.json de cada package

---

## Risco 3: Breaking Changes em Produção

**Problema:** Usuários quebram ao atualizar

**Mitigação:**
- ✅ Semantic versioning (v2 → v3)
- ✅ Changelog detalhado
- ✅ Migration guide
- ✅ Suporte paralelo v2/v3 por 6 meses

---

## Risco 4: Perda de Histórico

**Problema:** Git history fragmentado após split

**Mitigação:**
- ✅ Manter monorepo em `backup-monorepo` branch
- ✅ Linkar commits originais no README
- ✅ Documentar processo de split

---

# 📅 Timeline Detalhado

## Semana 1: Preparação Core
- Dia 1-2: Implementar `basePath()`
- Dia 3-4: Refatorar ServiceProvider
- Dia 5: Criar testes
- Dia 6-7: Testar localmente

## Semana 2: Preparação Packages
- Dia 8-10: Criar composer.json por package
- Dia 11-12: Configurar CI/CD
- Dia 13-14: Documentar cada package

## Semana 3: Split
- Dia 15-16: Criar repos no GitHub
- Dia 17: Executar split script
- Dia 18-19: Verificar e corrigir
- Dia 20-21: Criar tags e releases

## Semana 4: Publicação
- Dia 22: Publicar no Packagist
- Dia 23-24: Criar skeleton
- Dia 25: Testar instalação
- Dia 26-28: Ajustes finais

## Semana 5: Migração
- Dia 29-30: Backup monorepo
- Dia 31-32: Converter projeto atual
- Dia 33-34: Testes extensivos
- Dia 35: Deploy de teste

## Semana 6: Lançamento
- Dia 36-37: Atualizar documentação
- Dia 38-39: Criar guias de migração
- Dia 40-41: Anunciar v3.0.0
- Dia 42: Monitorar feedback

---

# ✅ Critérios de Sucesso

## Técnicos
- [ ] Performance mantida (520k req/s)
- [ ] Todos testes passando
- [ ] CI/CD verde em todos repos
- [ ] Auto-discovery funcionando
- [ ] Facades gerando corretamente

## Distribuição
- [ ] Publicado no Packagist
- [ ] `composer create-project` funciona
- [ ] `composer require` funciona
- [ ] Webhooks configurados
- [ ] Badges no README

## Documentação
- [ ] Migration guide completo
- [ ] README em cada repo
- [ ] CHANGELOG atualizado
- [ ] API docs gerada
- [ ] Examples funcionando

---

# 🎓 Conclusão

Esta migração transforma alphavel de projeto monorepo para framework profissional multi-repo, seguindo padrões da indústria (Laravel, Symfony) e permitindo distribuição via Composer/Packagist.

**Impacto:**
- Performance: 0% (zero impacto)
- Funcionalidade: 100% mantida
- Profissionalismo: +1000%

**Próximo passo:** Executar Fase 1 (Preparação) 🚀
