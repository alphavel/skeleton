# Estrutura do Projeto Alphavel

Esta é a estrutura padrão de uma aplicação Alphavel (microserviço API):

```
alphavel-app/
├── app/
│   └── Controllers/              # Controllers da aplicação
│       └── ExampleController.php # Controller de exemplo com REST API CRUD
│
├── routes/
│   └── api.php                   # Todas as rotas da API
│
├── bootstrap/
│   ├── app.php                   # Bootstrap da aplicação
│   └── cache/                    # Cache de rotas e config
│       └── .gitignore
│
├── public/
│   └── index.php                 # Entry point para modo tradicional
│
├── config/
│   ├── app.php                   # Configuração geral
│   └── swoole.php                # Configuração do Swoole
│
├── storage/
│   ├── cache/                    # Cache da aplicação
│   ├── logs/                     # Logs da aplicação
│   └── framework/                # Arquivos internos do framework
│
├── tests/                        # Testes automatizados
│   ├── Feature/
│   └── Unit/
│
├── vendor/                       # Dependências do Composer
│
├── .env                          # Variáveis de ambiente (não versionado)
├── .env.example                  # Exemplo de variáveis de ambiente
├── composer.json                 # Dependências do projeto
├── docker-compose.yml            # Configuração Docker para produção
├── docker-compose.dev.yml        # Configuração Docker para desenvolvimento
├── Dockerfile                    # Imagem Docker
├── alphavel                      # CLI do framework
└── README.md                     # Documentação
```

## 📦 Pacotes Opcionais

O skeleton vem mínimo. Adicione apenas o que precisa:

```bash
# Database (MySQL/PostgreSQL com connection pooling)
./alphavel package:add database

# Cache (Redis/Memcached)
./alphavel package:add cache

# Logging (Monolog)
./alphavel package:add logging

# Events (Event dispatcher)
./alphavel package:add events

# Validation (Request validation)
./alphavel package:add validation
```

## 📁 Descrição dos Diretórios

### `app/`
Contém toda a lógica da aplicação:
- **Controllers/**: Classes que gerenciam as requisições HTTP

### `routes/`
Define as rotas da API:
- **api.php**: Todas as rotas da API (REST endpoints)

### `bootstrap/`
Inicialização da aplicação:
- **app.php**: Configura e inicia o framework
- **cache/**: Armazena rotas e configurações compiladas para performance

### `public/`
Arquivos acessíveis publicamente:
- **index.php**: Entry point para servidor web tradicional (Apache/Nginx)

### `config/`
Arquivos de configuração:
- **app.php**: Configurações gerais (nome, ambiente, timezone, providers)
- **swoole.php**: Configurações do servidor Swoole (workers, coroutines, etc)

> **Nota**: Outros arquivos de config (database.php, cache.php, etc) são criados automaticamente quando você instala os pacotes correspondentes via `./alphavel package:add`

### `storage/`
Armazenamento de arquivos temporários:
- **cache/**: Cache de dados da aplicação
- **logs/**: Arquivos de log
- **framework/**: Arquivos internos do framework (sessions, views compiladas, etc)

### `tests/`
Testes automatizados:
- **Feature/**: Testes de funcionalidades completas
- **Unit/**: Testes unitários de classes individuais

## 🎯 Arquivos Importantes

### `.env`
Variáveis de ambiente sensíveis (não commitar!):
```bash
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:9501

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alphavel
DB_USERNAME=root
DB_PASSWORD=secret
```

### `composer.json`
Define dependências do projeto:
```json
{
    "require": {
        "alphavel/alphavel": "^1.0",
        "alphavel/database": "^1.0",
        "alphavel/cache": "^1.0"
    }
}
```

### `alphavel` (CLI)
Ferramenta de linha de comando:
```bash
./alphavel serve              # Inicia servidor Swoole
./alphavel package:add cache  # Adiciona pacote
./alphavel route:cache        # Compila rotas
```

## 🚦 Fluxo de Requisição

### Modo Swoole (Produção/Desenvolvimento)
```
Cliente HTTP
    ↓
Swoole Server (porta 9501)
    ↓
bootstrap/app.php
    ↓
Router → Controller
    ↓
Response JSON/HTML
    ↓
Cliente HTTP
```

### Modo Tradicional (FPM/Apache)
```
Cliente HTTP
    ↓
Servidor Web (Apache/Nginx)
    ↓
public/index.php
    ↓
bootstrap/app.php
    ↓
Router → Controller
    ↓
Response JSON/HTML
    ↓
Cliente HTTP
```

## 📝 Exemplos de Uso

### Criar Controller
```php
<?php
// app/Controllers/UserController.php

namespace App\Controllers;

use Alphavel\Framework\Controller;
use Alphavel\Framework\Request;
use Alphavel\Framework\Response;

class UserController extends Controller
{
    public function index()
    {
        return Response::make()->json([
            'users' => [
                ['id' => 1, 'name' => 'John'],
                ['id' => 2, 'name' => 'Jane']
            ]
        ]);
    }

    public function show(int $id)
    {
        return Response::make()->json([
            'user' => ['id' => $id, 'name' => 'John Doe']
        ]);
    }
}
```

### Adicionar Rotas
```php
<?php
// routes/api.php

use Alphavel\Framework\Router;
use Alphavel\Framework\Response;

/** @var Router $router */

// REST API routes
$router->get('/users', 'App\Controllers\UserController@index');
$router->get('/users/{id}', 'App\Controllers\UserController@show');
$router->post('/users', 'App\Controllers\UserController@store');
$router->put('/users/{id}', 'App\Controllers\UserController@update');
$router->delete('/users/{id}', 'App\Controllers\UserController@destroy');

// Ou com closure
$router->get('/hello', function () {
    return Response::make()->json(['message' => 'Hello!']);
});
```

### Usar Database (após instalar o pacote)

Primeiro instale o pacote:
```bash
./alphavel package:add database
```

Depois use em seus controllers:
```php
<?php

use Alphavel\Database\DB;

// Query simples
$users = DB::query('SELECT * FROM users WHERE active = ?', [1]);

// Query Builder
$user = DB::table('users')
    ->where('email', 'john@example.com')
    ->first();

// Transaction
DB::transaction(function () {
    DB::execute('INSERT INTO users (name) VALUES (?)', ['John']);
    DB::execute('INSERT INTO logs (action) VALUES (?)', ['user_created']);
});
```

## 🔧 Comandos Úteis

```bash
# Desenvolvimento
./alphavel serve                    # Iniciar servidor
./alphavel serve --host=0.0.0.0    # Servidor acessível externamente

# Packages
./alphavel package:add database    # Adicionar pacote
./alphavel package:discover        # Re-descobrir pacotes

# Performance
./alphavel route:cache             # Compilar rotas
./alphavel route:clear             # Limpar cache de rotas

# Docker
docker-compose up                  # Produção
docker-compose -f docker-compose.dev.yml up  # Desenvolvimento
```

## 📚 Próximos Passos

1. **Leia a documentação**: [README.md](README.md)
2. **Explore os exemplos**: Controllers em `app/Controllers/`
3. **Configure o banco**: `config/database.php` e `.env`
4. **Crie suas rotas**: Edite `routes/web.php`
5. **Desenvolva controllers**: Crie em `app/Controllers/`

## 🆘 Problemas Comuns

Veja [TROUBLESHOOTING.md](TROUBLESHOOTING.md) para soluções de problemas comuns.
