#!/bin/bash

# Script para atualizar dependências alphavel/core → alphavel/alphavel no Packagist
GITHUB_ORG="alphavel"

echo "========================================"
echo "  Atualizando dependências"
echo "  alphavel/core → alphavel/alphavel"
echo "========================================"
echo ""

update_dependency() {
    local repo_name=$1
    local temp_dir="/tmp/alphavel-packagist-${repo_name}"
    
    echo "📦 Atualizando: ${repo_name}"
    
    rm -rf "$temp_dir"
    git clone "https://github.com/${GITHUB_ORG}/${repo_name}.git" "$temp_dir"
    cd "$temp_dir" || return 1
    
    # Verifica se composer.json existe e tem a dependência
    if [ -f "composer.json" ] && grep -q '"alphavel/alphavel"' composer.json; then
        echo "   Alterando constraint ^2.0 → dev-main"
        
        # Altera a constraint de versão para dev-main
        sed -i 's/"alphavel\/alphavel": "\^2\.0"/"alphavel\/alphavel": "dev-main"/g' composer.json
        
        git add composer.json
        git commit -m "Update dependency constraint: ^2.0 → dev-main

Use dev-main until v2.0 is tagged.
"
        git push origin main
        
        echo "   ✓ ${repo_name} atualizado"
    else
        echo "   ⊘ Não precisa de atualização"
    fi
    
    cd - > /dev/null
    rm -rf "$temp_dir"
    echo ""
}

# Atualiza todos os pacotes que dependem do core
for repo in cache database events logging validation support; do
    update_dependency "$repo"
done

echo "========================================"
echo "  Concluído!"
echo "========================================"
echo ""
echo "Agora todos os pacotes usam: alphavel/alphavel"
echo ""
echo "Próximo passo:"
echo "1. Atualize cada pacote no Packagist.org"
echo "2. Rode: composer update --no-cache"
echo ""
