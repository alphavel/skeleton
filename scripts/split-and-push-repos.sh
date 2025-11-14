#!/bin/bash

# Script para fazer split do monorepo e push para os repositórios individuais
# Requer: git instalado

GITHUB_ORG="alphavel"
MONOREPO_PATH="/home/arthur/dev/php/HP/pfast"

echo "========================================"
echo "  Split e Push dos Pacotes Alphavel"
echo "========================================"
echo ""

cd "$MONOREPO_PATH" || exit 1

# Função para split e push de um pacote
split_and_push() {
    local package_name=$1
    local package_path=$2
    local repo_url="https://github.com/${GITHUB_ORG}/${package_name}.git"
    
    echo "📦 Processando: ${package_name}"
    echo "   Caminho: ${package_path}"
    
    # Cria um diretório temporário para o split
    local temp_dir="/tmp/alphavel-split-${package_name}"
    rm -rf "$temp_dir"
    mkdir -p "$temp_dir"
    
    # Copia os arquivos do pacote
    echo "   Copiando arquivos..."
    cp -r "${MONOREPO_PATH}/${package_path}"/* "$temp_dir/" 2>/dev/null || true
    
    # Verifica se há arquivos
    if [ ! "$(ls -A $temp_dir)" ]; then
        echo "   ✗ Nenhum arquivo encontrado em ${package_path}"
        return 1
    fi
    
    # Inicializa git no diretório temporário
    cd "$temp_dir" || return 1
    git init
    git add .
    git commit -m "Initial commit - Alphavel ${package_name} package

Extracted from monorepo: github.com/alphavel/framework
Date: $(date '+%Y-%m-%d')
"
    
    # Adiciona o remote e faz push
    echo "   Fazendo push para ${repo_url}..."
    git remote add origin "$repo_url"
    git branch -M main
    
    if git push -u origin main --force; then
        echo "   ✓ Push realizado com sucesso"
        cd "$MONOREPO_PATH"
        rm -rf "$temp_dir"
        return 0
    else
        echo "   ✗ Erro ao fazer push"
        cd "$MONOREPO_PATH"
        return 1
    fi
}

# Array de pacotes (nome:caminho)
declare -a packages=(
    "core:packages/core/src"
    "cache:packages/cache/src"
    "database:packages/database/src"
    "events:packages/events/src"
    "logging:packages/logging/src"
    "validation:packages/validation/src"
    "support:packages/support/src"
)

success_count=0
fail_count=0

# Processa cada pacote
for package_info in "${packages[@]}"; do
    IFS=':' read -r package_name package_path <<< "$package_info"
    
    if split_and_push "$package_name" "$package_path"; then
        ((success_count++))
    else
        ((fail_count++))
    fi
    echo ""
done

# Skeleton precisa de tratamento especial (app completo)
echo "📦 Processando: skeleton (aplicação completa)"
temp_dir="/tmp/alphavel-split-skeleton"
rm -rf "$temp_dir"
mkdir -p "$temp_dir"

# Copia estrutura da aplicação
echo "   Copiando estrutura da aplicação..."
cp -r "$MONOREPO_PATH/app" "$temp_dir/"
cp -r "$MONOREPO_PATH/bootstrap" "$temp_dir/"
cp -r "$MONOREPO_PATH/config" "$temp_dir/"
cp -r "$MONOREPO_PATH/public" "$temp_dir/"
cp -r "$MONOREPO_PATH/routes" "$temp_dir/"
cp -r "$MONOREPO_PATH/storage" "$temp_dir/"
mkdir -p "$temp_dir/tests"
cp "$MONOREPO_PATH/composer.json" "$temp_dir/"
cp "$MONOREPO_PATH/README.md" "$temp_dir/"
cp "$MONOREPO_PATH/phpunit.xml" "$temp_dir/" 2>/dev/null || true

cd "$temp_dir" || exit 1
git init
git add .
git commit -m "Initial commit - Alphavel Skeleton Application

Complete application structure for Alphavel Framework
Extracted from monorepo: github.com/alphavel/framework
Date: $(date '+%Y-%m-%d')
"

git remote add origin "https://github.com/${GITHUB_ORG}/skeleton.git"
git branch -M main

if git push -u origin main --force; then
    echo "   ✓ Skeleton push realizado com sucesso"
    ((success_count++))
else
    echo "   ✗ Erro ao fazer push do skeleton"
    ((fail_count++))
fi

cd "$MONOREPO_PATH"
rm -rf "$temp_dir"
echo ""

# Sumário
echo "========================================"
echo "  Sumário"
echo "========================================"
echo "✓ Push com sucesso: ${success_count}"
[ $fail_count -gt 0 ] && echo "✗ Falhas: ${fail_count}"
echo ""
echo "Repositórios disponíveis em:"
echo "  https://github.com/orgs/${GITHUB_ORG}/repositories"
echo ""
