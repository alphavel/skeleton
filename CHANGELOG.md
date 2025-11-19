# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [1.0.1] - 2025-11-19

### 🐛 Correções Críticas

- **facades.php agora é opcional**: Removido do autoload do Composer, eliminando erro fatal em containers Docker
- **Dockerfile corrigido**: Diretórios criados antes do `composer install` para evitar erro de arquivo não encontrado
- **Porta padronizada em 9999**: Todas as configurações agora usam porta 9999 consistentemente

### 📝 Alterações

- `composer.json`: Removido `storage/framework/facades.php` da seção `autoload.files`
- `Dockerfile`: Ordem de execução corrigida - criar diretórios antes do composer
- `config/app.php`: Porta padrão mudada de 9501 para 9999
- `docker-compose.yml`: Mapeamento de porta atualizado para 9999:9999
- `docker-compose.dev.yml`: Mapeamento de porta atualizado para 9999:9999
- Scripts do composer: Removida criação automática de facades.php

### 📚 Documentação

- Adicionado `FACADES.md`: Documentação completa sobre facades e quando usá-las
- README.md: Todas as referências de porta atualizadas para 9999
- DESENVOLVIMENTO_LOCAL.md: Portas atualizadas

### 🔧 Impacto

Esta versão corrige o problema crítico de containers Docker em loop de reinicialização causado pelo arquivo `facades.php` ausente. O framework agora é totalmente modular e não requer facades para funcionar.

## [1.0.0] - 2025-11-19

### ✨ Lançamento Inicial

Primeira versão estável do Alphavel Framework - framework PHP moderno baseado em Swoole.

### 🎯 Principais Características

- **Setup automático via Composer**: Processo idêntico ao Laravel, sem etapas manuais
- **Extensão Swoole opcional**: Permite desenvolvimento com Docker sem instalação local
- **Performance excepcional**: Até 520.000+ requisições por segundo com Swoole
- **Arquitetura limpa**: Inspirada no Laravel, fácil de aprender e usar

### ✨ Adicionado

- **Setup automático via Composer**: Processo idêntico ao Laravel
  - `.env` copiado automaticamente ao criar projeto
  - Diretórios criados automaticamente (`storage/*`, `bootstrap/cache`)
  - Permissões definidas automaticamente (0777 em storage)
  - Arquivo `facades.php` gerado automaticamente
  
- **Detecção inteligente de ambiente**:
  - Detecta presença do Swoole automaticamente
  - Mostra instruções contextuais baseadas no ambiente
  - Orienta usuário para Docker se Swoole não estiver instalado

- **Docker Dev Environment**:
  - `docker-compose.dev.yml` para desenvolvimento sem Swoole local
  - Instalação automática de Swoole no container
  - Configuração de dependências automática
  - Comandos Make simplificados (`make dev`, `make dev-stop`, etc.)

- **Scripts Composer organizados**:
  - `@create-directories`: Cria estrutura de diretórios
  - `@set-permissions`: Define permissões corretas
  - `@check-environment`: Verifica Swoole e mostra instruções
  - `@show-next-steps`: Guia contextual de próximos passos

### 📝 Alterado

- **README.md reescrito**: 
  - Foco em "Laravel-like experience"
  - Comparação direta Laravel vs Alphavel
  - Seção "O que Acontece Automaticamente"
  - Instruções simplificadas

- **Fluxo de instalação**: 
  - Antes: `composer create-project` → `./setup.sh` → `php public/index.php`
  - Agora: `composer create-project` → `php public/index.php` (ou `make dev`)

- **Mensagens de instalação**: Contextuais e guiadas baseadas no ambiente

### 🐛 Corrigido

- Problema de diretórios não criados durante instalação
- Erro de permissões em `storage/` e `bootstrap/cache/`
- `.env` não copiado automaticamente
- `facades.php` não gerado em instalação limpa
- Dependência obrigatória do Swoole impedindo instalação via Docker

### 🔧 Infraestrutura

- CI/CD atualizado para novas versões
- Docker images otimizados
- Health checks adicionados em containers
- Makefile expandido com mais comandos

### 📚 Documentação

- Adicionado `DESENVOLVIMENTO_LOCAL.md`
- Adicionado `CHANGELOG.md`
- README expandido com seções de troubleshooting
- Comparação explícita com Laravel

---

[1.0.1]: https://github.com/alphavel/skeleton/releases/tag/v1.0.1
[1.0.0]: https://github.com/alphavel/skeleton/releases/tag/v1.0.0
[Unreleased]: https://github.com/alphavel/skeleton/compare/v1.0.1...HEAD
