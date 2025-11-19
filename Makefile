.PHONY: help install start stop restart logs logs-db shell composer clean rebuild test

.DEFAULT_GOAL := help

help: ## Mostra esta mensagem de ajuda
	@echo "Comandos disponíveis para Alphavel Framework:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Instala o projeto completo (primeira vez)
	@bash install.sh .

start: ## Inicia os containers
	@echo "🚀 Iniciando containers..."
	@docker-compose up -d
	@echo "✅ Containers iniciados"
	@echo "🌐 Aplicação disponível em: http://localhost:8080"

stop: ## Para os containers
	@echo "⏸️  Parando containers..."
	@docker-compose down
	@echo "✅ Containers parados"

restart: ## Reinicia os containers
	@echo "🔄 Reiniciando containers..."
	@docker-compose restart
	@echo "✅ Containers reiniciados"

logs: ## Mostra os logs da aplicação em tempo real
	@docker-compose logs -f app

logs-db: ## Mostra os logs do banco de dados
	@docker-compose logs -f db

shell: ## Acessa o shell do container da aplicação
	@docker-compose exec app bash

shell-db: ## Acessa o shell do container do MySQL
	@docker-compose exec db mysql -u${DB_USERNAME:-alphavel} -p${DB_PASSWORD:-alphavel} ${DB_DATABASE:-alphavel}

composer: ## Executa comandos composer (uso: make composer ARGS="install")
	@docker-compose exec app composer $(ARGS)

composer-install: ## Instala dependências do composer
	@echo "📦 Instalando dependências..."
	@docker-compose exec app composer install
	@echo "✅ Dependências instaladas"

composer-update: ## Atualiza dependências do composer
	@echo "🔄 Atualizando dependências..."
	@docker-compose exec app composer update
	@echo "✅ Dependências atualizadas"

composer-dump: ## Regenera o autoload
	@echo "🔄 Regenerando autoload..."
	@docker-compose exec app composer dump-autoload -o
	@echo "✅ Autoload regenerado"

test: ## Executa os testes
	@echo "🧪 Executando testes..."
	@docker-compose exec app vendor/bin/phpunit

test-coverage: ## Executa os testes com cobertura
	@echo "🧪 Executando testes com cobertura..."
	@docker-compose exec app vendor/bin/phpunit --coverage-html coverage
	@echo "✅ Relatório de cobertura gerado em coverage/"

clean: ## Remove containers, volumes e arquivos temporários
	@echo "🧹 Limpando projeto..."
	@docker-compose down -v
	@rm -rf vendor storage/cache/* storage/logs/* storage/framework/cache/*
	@echo "✅ Limpeza concluída"

rebuild: ## Reconstrói os containers do zero
	@echo "🔨 Reconstruindo containers..."
	@docker-compose down
	@docker-compose build --no-cache
	@docker-compose up -d
	@echo "✅ Containers reconstruídos"

ps: ## Lista os containers em execução
	@docker-compose ps

status: ## Mostra o status dos containers
	@docker-compose ps
	@echo ""
	@echo "🌐 Aplicação: http://localhost:${APP_PORT:-8080}"
	@echo "🗄️  MySQL: localhost:${DB_PORT:-3306}"

fix-permissions: ## Corrige permissões de diretórios
	@echo "🔧 Corrigindo permissões..."
	@docker-compose exec app chmod -R 777 storage bootstrap/cache
	@echo "✅ Permissões corrigidas"

cache-clear: ## Limpa o cache da aplicação
	@echo "🧹 Limpando cache..."
	@rm -rf storage/cache/*
	@rm -rf storage/framework/cache/*
	@echo "✅ Cache limpo"

logs-clear: ## Limpa os logs da aplicação
	@echo "🧹 Limpando logs..."
	@rm -rf storage/logs/*
	@echo "✅ Logs limpos"

db-fresh: ## Recria o banco de dados (CUIDADO: apaga todos os dados)
	@echo "⚠️  Recriando banco de dados..."
	@docker-compose exec db mysql -uroot -p${DB_ROOT_PASSWORD:-root} -e "DROP DATABASE IF EXISTS ${DB_DATABASE:-alphavel}; CREATE DATABASE ${DB_DATABASE:-alphavel};"
	@echo "✅ Banco de dados recriado"

backup-db: ## Faz backup do banco de dados
	@echo "💾 Fazendo backup do banco de dados..."
	@mkdir -p backups
	@docker-compose exec db mysqldump -u${DB_USERNAME:-alphavel} -p${DB_PASSWORD:-alphavel} ${DB_DATABASE:-alphavel} > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ Backup salvo em backups/"

watch: ## Inicia o servidor e mostra os logs
	@docker-compose up

down: ## Para e remove os containers
	@echo "🛑 Parando e removendo containers..."
	@docker-compose down
	@echo "✅ Containers removidos"

up: ## Alias para start
up: start
