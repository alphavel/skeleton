#!/bin/bash

# Script para atualizar dependências de alphavel/core para alphavel/alphavel
GITHUB_ORG="alphavel"

echo "========================================"
echo "  Atualizando dependências"
echo "  alphavel/core → alphavel/alphavel"
echo "========================================"
echo ""

update_dependency() {
    local repo_name=$1
    local temp_dir="/tmp/alphavel-fix-${repo_name}"
    
    echo "📦 Atualizando: ${repo_name}"
    
    rm -rf "$temp_dir"
    git clone "https://github.com/${GITHUB_ORG}/${repo_name}.git" "$temp_dir"
    cd "$temp_dir" || return 1
    
    # Verifica se composer.json existe e tem a dependência
    if [ -f "composer.json" ] && grep -q '"alphavel/core"' composer.json; then
        echo "   Alterando alphavel/core → alphavel/alphavel"
        sed -i 's/"alphavel\/core"/"alphavel\/alphavel"/g' composer.json
        
        git add composer.json
        git commit -m "Update dependency: alphavel/core → alphavel/alphavel"
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
for repo in cache database events logging validation support skeleton; do
    update_dependency "$repo"
done

# Atualiza o próprio alphavel/alphavel
echo "📦 Atualizando: alphavel (package name)"
temp_dir="/tmp/alphavel-fix-alphavel"
rm -rf "$temp_dir"
git clone "https://github.com/${GITHUB_ORG}/alphavel.git" "$temp_dir"
cd "$temp_dir" || exit 1

if [ -f "composer.json" ]; then
    echo "   Alterando name: alphavel/core → alphavel/alphavel"
    sed -i 's/"name": "alphavel\/core"/"name": "alphavel\/alphavel"/g' composer.json
    
    git add composer.json
    git commit -m "Update package name: alphavel/core → alphavel/alphavel"
    git push origin main
    
    echo "   ✓ alphavel atualizado"
fi

cd - > /dev/null
rm -rf "$temp_dir"
echo ""

echo "========================================"
echo "  Concluído!"
echo "========================================"
echo ""
echo "Agora todos os pacotes usam: alphavel/alphavel"
echo ""
