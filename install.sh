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

# 5.1. Corrigir permissões para coincidir com usuário host
echo -e "${YELLOW}🔒 Configurando permissões (UID/GID do host)...${NC}"
USER_ID=$(id -u)
GROUP_ID=$(id -g)

# Exportar para docker-compose usar
export USER_ID
export GROUP_ID

# Garantir que os diretórios existam e tenham as permissões corretas
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R ${USER_ID}:${GROUP_ID} storage bootstrap/cache 2>/dev/null || true

echo -e "${GREEN}✓ Usando UID=${USER_ID} GID=${GROUP_ID}${NC}"

# 6. Copiar .env.example para .env se não existir
if [ -f .env.example ] && [ ! -f .env ]; then
    echo -e "${YELLOW}⚙️  Copiando configurações de ambiente...${NC}"
    cp .env.example .env
    
    # Adicionar USER_ID e GROUP_ID ao .env
    echo "" >> .env
    echo "# Docker User Configuration" >> .env
    echo "USER_ID=${USER_ID}" >> .env
    echo "GROUP_ID=${GROUP_ID}" >> .env
fi

# 8. Construir e iniciar containers
echo -e "${YELLOW}🐳 Construindo e iniciando containers Docker...${NC}"
docker-compose up -d --build

# 9. Aguardar inicialização
echo -e "${YELLOW}⏳ Aguardando servidor inicializar...${NC}"
sleep 8

# 10. Verificar se está rodando (usando /json endpoint)
MAX_ATTEMPTS=10
ATTEMPT=0
PORT=${APP_PORT:-9999}

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    if curl -s http://localhost:${PORT}/json > /dev/null 2>&1; then
        echo -e "\n${GREEN}✅ Alphavel instalado com sucesso!${NC}"
        echo -e "${GREEN}🌐 Acesse: http://localhost:${PORT}${NC}\n"
        
        echo -e "${BLUE}📚 Endpoints disponíveis:${NC}"
        echo -e "   ${YELLOW}http://localhost:${PORT}/json${NC}      - JSON response"
        echo -e "   ${YELLOW}http://localhost:${PORT}/plaintext${NC} - Plain text response"
        echo -e "   ${YELLOW}http://localhost:${PORT}/db${NC}        - Database query (se configurado)\n"
        
        echo -e "${BLUE}📋 Comandos úteis:${NC}"
        echo -e "   ${YELLOW}make help${NC}      - Ver todos os comandos disponíveis"
        echo -e "   ${YELLOW}make logs${NC}      - Ver logs do servidor"
        echo -e "   ${YELLOW}make shell${NC}     - Acessar shell do container"
        echo -e "   ${YELLOW}make restart${NC}   - Reiniciar servidor\n"
        
        echo -e "${BLUE}🔧 Troubleshooting:${NC}"
        echo -e "   ${YELLOW}make fix-permissions${NC} - Corrigir permissões de arquivos\n"
        
        exit 0
    fi
    
    ATTEMPT=$((ATTEMPT + 1))
    echo -e "${YELLOW}   Tentativa ${ATTEMPT}/${MAX_ATTEMPTS}...${NC}"
    sleep 2
done

echo -e "${YELLOW}⚠️  Servidor iniciado, mas a verificação de saúde falhou.${NC}"
echo -e "${YELLOW}🌐 Tente acessar manualmente:${NC}"
echo -e "   ${YELLOW}http://localhost:${PORT}/json${NC}\n"

echo -e "${BLUE}📋 Verificar logs:${NC}"
echo -e "   ${YELLOW}docker-compose logs -f app${NC}\n"

echo -e "${BLUE}🔧 Se houver problemas de permissão:${NC}"
echo -e "   ${YELLOW}make fix-permissions${NC}\n"

# Mostrar logs para debug
echo -e "${BLUE}📋 Últimas linhas dos logs:${NC}"
docker-compose logs --tail=20 app
