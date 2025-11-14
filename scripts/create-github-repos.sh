#!/bin/bash

GITHUB_ORG="alphavel"

echo "========================================"
echo "  Criando Repositórios Alphavel"
echo "========================================"
echo ""

create_repo() {
    local repo_name=$1
    local description=$2
    
    echo "📦 Criando repositório: ${repo_name}"
    
    if gh repo view "${GITHUB_ORG}/${repo_name}" &> /dev/null; then
        echo "   ✓ Repositório já existe"
        return 0
    fi
    
    if gh repo create "${GITHUB_ORG}/${repo_name}" \
        --public \
        --description "${description}"; then
        echo "   ✓ Repositório criado"
        return 0
    else
        echo "   ✗ Erro ao criar repositório"
        return 1
    fi
}

declare -a repos=(
    "core:Core components of Alphavel Framework"
    "cache:Cache package for Alphavel Framework"
    "database:Database package for Alphavel Framework"
    "events:Event dispatcher for Alphavel Framework"
    "logging:Logging package for Alphavel Framework"
    "validation:Validation package for Alphavel Framework"
    "support:Support utilities for Alphavel Framework"
    "skeleton:Application skeleton for Alphavel Framework"
)

success_count=0
fail_count=0

for repo_info in "${repos[@]}"; do
    IFS=':' read -r repo_name description <<< "$repo_info"
    
    if create_repo "$repo_name" "$description"; then
        ((success_count++))
    else
        ((fail_count++))
    fi
    echo ""
done

echo "========================================"
echo "  Sumário"
echo "========================================"
echo "✓ Criados/Existentes: ${success_count}"
[ $fail_count -gt 0 ] && echo "✗ Falhas: ${fail_count}"
echo ""
echo "Visualize em: https://github.com/orgs/${GITHUB_ORG}/repositories"
echo ""
