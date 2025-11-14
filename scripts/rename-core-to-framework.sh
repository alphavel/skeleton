#!/bin/bash

# Script para renomear Alphavel\Core para Alphavel\Framework
# Similar ao rename Pfast→Alphavel, mas agora Core→Framework

echo "========================================"
echo "  Renomeando Core → Framework"
echo "========================================"
echo ""

MONOREPO_PATH="/home/arthur/dev/php/HP/pfast"
cd "$MONOREPO_PATH" || exit 1

# Contadores
php_count=0
json_count=0
md_count=0
other_count=0

echo "📝 Processando arquivos PHP..."
find . -type f -name "*.php" \
    -not -path "./vendor/*" \
    -not -path "./.git/*" \
    -not -path "./storage/framework/facades.php" | while read -r file; do
    if grep -q "Alphavel\\\\Core\|Alphavel/Core" "$file"; then
        sed -i 's/Alphavel\\Core/Alphavel\\Framework/g' "$file"
        sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' "$file"
        echo "  ✓ $file"
        ((php_count++))
    fi
done

echo ""
echo "📝 Processando arquivos JSON (composer.json)..."
find . -type f -name "composer.json" \
    -not -path "./vendor/*" \
    -not -path "./.git/*" | while read -r file; do
    if grep -q "Alphavel\\\\\\\\Core\|Alphavel/Core" "$file"; then
        sed -i 's/Alphavel\\\\Core/Alphavel\\\\Framework/g' "$file"
        sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' "$file"
        echo "  ✓ $file"
        ((json_count++))
    fi
done

echo ""
echo "📝 Processando arquivos Markdown..."
find . -type f -name "*.md" \
    -not -path "./vendor/*" \
    -not -path "./.git/*" | while read -r file; do
    if grep -q "Alphavel\\\\Core\|Alphavel/Core" "$file"; then
        sed -i 's/Alphavel\\Core/Alphavel\\Framework/g' "$file"
        sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' "$file"
        echo "  ✓ $file"
        ((md_count++))
    fi
done

echo ""
echo "📝 Processando outros arquivos de configuração..."
find . -type f \( -name "*.xml" -o -name "*.yml" -o -name "*.yaml" \) \
    -not -path "./vendor/*" \
    -not -path "./.git/*" | while read -r file; do
    if grep -q "Alphavel\\\\Core\|Alphavel/Core" "$file"; then
        sed -i 's/Alphavel\\Core/Alphavel\\Framework/g' "$file"
        sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' "$file"
        echo "  ✓ $file"
        ((other_count++))
    fi
done

echo ""
echo "🔄 Regenerando facades com novo namespace..."
php generate-facades.php

echo ""
echo "========================================"
echo "  Renomeação Concluída!"
echo "========================================"
echo ""
echo "Arquivos processados no monorepo local"
echo ""
echo "Próximo passo: Atualizar repositórios GitHub"
echo ""
