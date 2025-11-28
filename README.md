# Alphavel Application Skeleton

> Minimal application starter for Alphavel Framework

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue.svg)](https://php.net)
[![Swoole](https://img.shields.io/badge/swoole-required-red.svg)](https://www.swoole.co.uk/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 🚀 Performance: Proactive by Default (v1.0.6+)

**Alphavel delivers 22k req/s out of the box** - no manual tuning required!

### What's Optimized Automatically

✅ **BASE mode** - 29% faster than PROCESS mode  
✅ **CPU × 2 workers** - Optimal parallelism  
✅ **Infinite max_request** - No restart overhead  
✅ **APCu autoloader** - Cached class locations  
✅ **Aggressive OPcache warm-up** - Hot cache from start  

**Benchmarks:** 22k req/s (complex routes) | 520k+ req/s (simple routes)

---

## ⚡ Quick Start

### Create New Project

```bash
composer create-project alphavel/skeleton my-app
cd my-app
php public/index.php
```

Visit: http://localhost:9999

### Docker (Recommended for Development)

```bash
# No Swoole installation required!
docker-compose -f docker-compose.dev.yml up

# Access
curl http://localhost:9999
```

### 🐳 Installing Without PHP/Swoole Locally

If you don't have PHP or Swoole installed on your machine:

```bash
# 1. Install project (ignoring platform requirements)
composer create-project alphavel/skeleton my-app --ignore-platform-reqs

# 2. Go to project directory
cd my-app

# 3. Start with Docker (composer install runs inside container automatically)
docker-compose up

# The container will:
# ✅ Detect missing vendor/autoload.php
# ✅ Run composer install automatically
# ✅ Copy any missing skeleton files
# ✅ Start the server

# Access
curl http://localhost:9999
```

**Note:** The Docker entrypoint automatically handles missing dependencies and skeleton files.

## ⚠️ Composer cache permissions (host)

During `composer create-project` you may see warnings like:

```
Cannot create cache directory /home/USER/.composer/cache/... or directory is not writable. Proceeding without cache.
```

This is harmless for the project creation itself, but can be confusing. Recommendations:

- Fix permissions for the Composer cache directory on your host:

```bash
# Replace $USER with your username if needed
mkdir -p "$HOME/.composer/cache"
chown -R "$USER:$USER" "$HOME/.composer"
```

- Or avoid host Composer entirely by using the provided Docker image (recommended):

```bash
docker-compose -f docker-compose.dev.yml up
```

If you must ignore platform requirements when creating the project (not recommended for production), use:

```bash
composer create-project alphavel/skeleton my-app --ignore-platform-reqs
```


---

## 📦 Optional Packages

```bash
composer require alphavel/database    # Query Builder + ORM
composer require alphavel/cache       # Redis, File caching
composer require alphavel/validation  # Input validation
composer require alphavel/events      # Event dispatcher
composer require alphavel/logging     # PSR-3 logger
composer require alphavel/support     # Collections, helpers
```

After installing, update `.env` with appropriate connection settings.

---

## 📁 Project Structure

```
my-app/
├── app/
│   └── Controllers/         # HTTP controllers
├── config/
│   ├── app.php             # App configuration
│   └── swoole.php          # Swoole server config (proactive defaults)
├── public/
│   └── index.php           # Entry point
├── routes/
│   └── api.php             # API routes
├── storage/
│   ├── framework/          # Framework cache
│   └── logs/               # Application logs
├── Dockerfile              # Production image (APCu + OPcache)
└── docker-compose.yml      # Docker orchestration
```

---

## 📚 Documentation

**Full documentation**: https://github.com/alphavel/documentation

- [Getting Started](https://github.com/alphavel/documentation/blob/master/core/getting-started.md)
- [Performance](https://github.com/alphavel/documentation/blob/master/core/performance.md)
- [Deployment](https://github.com/alphavel/documentation/blob/master/deployment/production.md)
- [Configuration](https://github.com/alphavel/documentation/blob/master/core/configuration.md)

---

## 📄 License

MIT License
