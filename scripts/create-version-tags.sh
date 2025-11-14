#!/bin/bash

# Script para criar tags v2.0.0 em todos os repositórios
GITHUB_ORG="alphavel"
VERSION="v2.0.0"

echo "========================================"
echo "  Criando tags ${VERSION}"
echo "========================================"
echo ""

create_tag() {
    local repo_name=$1
    local temp_dir="/tmp/alphavel-tag-${repo_name}"
    
    echo "📦 ${repo_name}"
    
    rm -rf "$temp_dir"
    git clone "https://github.com/${GITHUB_ORG}/${repo_name}.git" "$temp_dir" --depth=1
    cd "$temp_dir" || return 1
    
    # Cria tag anotada
    git tag -a "${VERSION}" -m "Release ${VERSION}

First stable release of Alphavel ${repo_name^} package.

Features:
- Multi-repo architecture
- PSR compliant (PSR-3, PSR-4, PSR-11)
- Auto-discovery via Composer
- Production ready

Framework: Alphavel v2.0 - Ultra-fast PHP framework (520k+ req/s)"
    
    # Push da tag
    if git push origin "${VERSION}"; then
        echo "   ✓ Tag ${VERSION} criada e enviada"
    else
        echo "   ✗ Erro ao enviar tag"
        cd - > /dev/null
        return 1
    fi
    
    cd - > /dev/null
    rm -rf "$temp_dir"
    echo ""
}

# Lista de repositórios
repos=(
    "alphavel"
    "cache"
    "database"
    "events"
    "logging"
    "validation"
    "support"
    "skeleton"
)

# Cria tags em todos os repos
for repo in "${repos[@]}"; do
    create_tag "$repo"
done

echo "========================================"
echo "  Concluído!"
echo "========================================"
echo ""
echo "Tags ${VERSION} criadas em todos os repositórios"
echo ""
echo "Próximo passo:"
echo "- Aguarde alguns minutos para o Packagist detectar as tags"
echo "- Depois poderá usar: composer require alphavel/alphavel:^2.0"
echo ""
