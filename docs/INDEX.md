# Documentation Index

Welcome to alphavel Framework v2.0 documentation.

## 📚 Available Guides

### Core Documentation

- **[../README.md](../README.md)** - Quick start, features, and installation
- **[EXTENSIBILITY.md](EXTENSIBILITY.md)** - Creating custom plugins and service providers
- **[FACADES.md](FACADES.md)** - Auto-facade system implementation
- **[PERFORMANCE-OPTIMIZATION.md](PERFORMANCE-OPTIMIZATION.md)** - Performance tuning and optimization
- **[PSR-COMPLIANCE.md](PSR-COMPLIANCE.md)** - PSR standards implementation details

### Package Documentation

- **[../packages/cache/README.md](../packages/cache/README.md)** - Cache plugin guide
- **[../packages/database/README.md](../packages/database/README.md)** - Database plugin guide
- **[../packages/validation/README.md](../packages/validation/README.md)** - Validation plugin guide
- **[../packages/events/README.md](../packages/events/README.md)** - Events plugin guide
- **[../packages/logging/README.md](../packages/logging/README.md)** - Logging plugin guide (PSR-3)
- **[../packages/support/README.md](../packages/support/README.md)** - Support utilities guide

---

## 🚀 Quick Links

### Getting Started
1. [Installation](../README.md#-quick-start)
2. [Hello World Example](../README.md#hello-world)
3. [Architecture Overview](../README.md#-architecture)

### Core Concepts
- [Service Providers](../README.md#service-providers)
- [Auto-Facades](../README.md#auto-facades)
- [Models & Database](../README.md#models--database)
- [Collections](../README.md#collections)
- [Validation](../README.md#validation)

### Advanced Topics
- [Creating Custom Plugins](EXTENSIBILITY.md)
- [Performance Optimization](PERFORMANCE-OPTIMIZATION.md)
- [PSR Compliance](PSR-COMPLIANCE.md)

### Testing & Deployment
- [Testing](../README.md#-testing)
- [Deployment](../README.md#-deployment)

---

## 📊 Framework Stats

| Metric | Value |
|--------|-------|
| Performance | 520,000 req/s (core only) |
| Memory | 0.3MB - 4MB (modular) |
| PSR Compliance | 5/7 (71%) |
| Packages | 7 (1 required + 6 optional) |
| PHP Version | ≥8.1 |

---

## 🎯 Documentation by Use Case

### Building an API Gateway
- Start with [Core Only Setup](../README.md#quick-start)
- Add [Validation](../packages/validation/README.md) for input
- Use [Cache](../packages/cache/README.md) for rate limiting
- Expected: **520k req/s, 0.8MB**

### Building a REST API with Database
- Use [Database Plugin](../packages/database/README.md)
- Add [Validation](../packages/validation/README.md)
- Use [Cache](../packages/cache/README.md) for queries
- Expected: **480k req/s, 1.2MB**

### Building a Full-Stack Application
- Enable all plugins in `config/app.php`
- Use [Events](../packages/events/README.md) for decoupling
- Use [Logging](../packages/logging/README.md) for debugging
- Expected: **387k req/s, 4MB**

---

## 🔧 Development Tools

### CLI Commands
```bash
./alphavel serve              # Start server
./alphavel make:controller    # Generate controller
./alphavel make:model         # Generate model
./alphavel optimize           # Optimize for production
./alphavel route:list         # List all routes
```

### Testing
```bash
composer test              # Run PHPUnit tests
php verify-psr.php         # Verify PSR compliance
./vendor/bin/phpcs         # Check coding style
```

---

## 🤝 Contributing

Found an issue or want to improve documentation?

1. Edit the relevant `.md` file
2. Follow [Markdown best practices](https://www.markdownguide.org/)
3. Submit a PR with clear description

---

## 📞 Support

- **Issues**: GitHub Issues
- **Discussions**: GitHub Discussions
- **Documentation**: This directory

---

**Last Updated:** 13 Nov 2025  
**Version:** 2.0.0
