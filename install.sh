#!/bin/bash
set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

PROJECT_NAME="${1:-alphavel-app}"

echo -e "${BLUE}🚀 Instalando Alphavel Framework...${NC}\n"

# 1. Verificar Docker
echo -e "${YELLOW}📦 Verificando requisitos...${NC}"
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker não encontrado. Por favor, instale o Docker primeiro.${NC}"
    echo -e "${YELLOW}   Visite: https://docs.docker.com/get-docker/${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Docker encontrado${NC}"

# 2. Verificar Docker Compose
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null 2>&1; then
    echo -e "${RED}❌ Docker Compose não encontrado. Por favor, instale o Docker Compose primeiro.${NC}"
    echo -e "${YELLOW}   Visite: https://docs.docker.com/compose/install/${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Docker Compose encontrado${NC}\n"

# 3. Criar diretório do projeto se necessário
if [ "$PROJECT_NAME" != "." ] && [ ! -d "$PROJECT_NAME" ]; then
    echo -e "${YELLOW}📁 Criando diretório do projeto...${NC}"
    mkdir -p "$PROJECT_NAME"
    cd "$PROJECT_NAME"
else
    cd "$PROJECT_NAME"
fi

# 4. Criar projeto usando Composer
echo -e "${YELLOW}📦 Criando projeto Alphavel via Composer...${NC}"
docker run --rm -v "$(pwd)":/app -w /app composer:latest \
    composer create-project alphavel/skeleton . --ignore-platform-req=ext-swoole --no-interaction

# 5. Criar estrutura de diretórios
echo -e "${YELLOW}📁 Criando estrutura de diretórios...${NC}"
mkdir -p storage/framework storage/logs storage/cache bootstrap/cache
chmod -R 777 storage bootstrap/cache

# 6. Criar arquivo facades.php inicial
echo -e "${YELLOW}📝 Criando arquivo de facades...${NC}"
echo '<?php' > storage/framework/facades.php
chmod 777 storage/framework/facades.php

# 7. Copiar .env.example para .env se não existir
if [ -f .env.example ] && [ ! -f .env ]; then
    echo -e "${YELLOW}⚙️  Copiando configurações de ambiente...${NC}"
    cp .env.example .env
fi

# 8. Construir e iniciar containers
echo -e "${YELLOW}🐳 Construindo e iniciando containers Docker...${NC}"
docker-compose up -d --build

# 9. Aguardar inicialização
echo -e "${YELLOW}⏳ Aguardando servidor inicializar...${NC}"
sleep 8

# 10. Verificar se está rodando
MAX_ATTEMPTS=10
ATTEMPT=0
PORT=${APP_PORT:-8080}

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    if curl -s http://localhost:${PORT} > /dev/null 2>&1; then
        echo -e "\n${GREEN}✅ Alphavel instalado com sucesso!${NC}"
        echo -e "${GREEN}🌐 Acesse: http://localhost:${PORT}${NC}\n"
        
        echo -e "${BLUE}📚 Próximos passos:${NC}"
        echo -e "   1. Acesse a aplicação: ${YELLOW}http://localhost:${PORT}${NC}"
        echo -e "   2. Instale pacotes adicionais: ${YELLOW}make composer ARGS=\"require alphavel/database\"${NC}"
        echo -e "   3. Ver logs: ${YELLOW}make logs${NC}"
        echo -e "   4. Parar servidor: ${YELLOW}make stop${NC}\n"
        
        echo -e "${BLUE}📋 Comandos úteis:${NC}"
        echo -e "   ${YELLOW}make help${NC}      - Ver todos os comandos disponíveis"
        echo -e "   ${YELLOW}make logs${NC}      - Ver logs do servidor"
        echo -e "   ${YELLOW}make shell${NC}     - Acessar shell do container"
        echo -e "   ${YELLOW}make restart${NC}   - Reiniciar servidor\n"
        
        exit 0
    fi
    
    ATTEMPT=$((ATTEMPT + 1))
    echo -e "${YELLOW}   Tentativa ${ATTEMPT}/${MAX_ATTEMPTS}...${NC}"
    sleep 2
done

echo -e "${YELLOW}⚠️  Servidor iniciado, mas a verificação de saúde falhou.${NC}"
echo -e "${YELLOW}🌐 Tente acessar: http://localhost:${PORT}${NC}\n"

echo -e "${BLUE}📋 Verificar logs:${NC}"
echo -e "   ${YELLOW}docker-compose logs -f app${NC}\n"

# Mostrar logs para debug
echo -e "${BLUE}📋 Últimas linhas dos logs:${NC}"
docker-compose logs --tail=20 app
