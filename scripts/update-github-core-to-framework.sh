#!/bin/bash

# Script para atualizar todos os repositórios com Core → Framework
GITHUB_ORG="alphavel"
MONOREPO_PATH="/home/arthur/dev/php/HP/pfast"

echo "========================================"
echo "  Atualizando Repositórios GitHub"
echo "  Core → Framework"
echo "========================================"
echo ""

update_repo() {
    local repo_name=$1
    local source_path=$2
    local temp_dir="/tmp/alphavel-framework-${repo_name}"
    
    echo "📦 Atualizando: ${repo_name}"
    
    rm -rf "$temp_dir"
    git clone "https://github.com/${GITHUB_ORG}/${repo_name}.git" "$temp_dir"
    cd "$temp_dir" || return 1
    
    # Conta arquivos que precisam de atualização
    local changed=0
    
    # Atualiza arquivos PHP
    find . -type f -name "*.php" -not -path "./.git/*" | while read -r file; do
        if grep -q "Alphavel\\\\Core\|Alphavel/Core" "$file"; then
            sed -i 's/Alphavel\\Core/Alphavel\\Framework/g' "$file"
            sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' "$file"
            ((changed++))
        fi
    done
    
    # Atualiza composer.json
    if [ -f "composer.json" ]; then
        if grep -q "Alphavel\\\\\\\\Core\|Alphavel/Core" "composer.json"; then
            sed -i 's/Alphavel\\\\Core/Alphavel\\\\Framework/g' composer.json
            sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' composer.json
            ((changed++))
        fi
    fi
    
    # Atualiza README.md
    if [ -f "README.md" ]; then
        if grep -q "Alphavel\\\\Core\|Alphavel/Core" "README.md"; then
            sed -i 's/Alphavel\\Core/Alphavel\\Framework/g' README.md
            sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' README.md
            ((changed++))
        fi
    fi
    
    # Verifica se há mudanças
    if git diff --quiet; then
        echo "   ⊘ Nenhuma mudança necessária"
        cd - > /dev/null
        rm -rf "$temp_dir"
        return 0
    fi
    
    # Copia arquivos atualizados do monorepo se especificado
    if [ -n "$source_path" ] && [ -d "${MONOREPO_PATH}/${source_path}" ]; then
        echo "   Sincronizando com monorepo..."
        rm -rf ./*
        cp -r "${MONOREPO_PATH}/${source_path}"/* ./
    fi
    
    git add .
    git commit -m "Refactor: Rename Alphavel\\Core to Alphavel\\Framework

Follow Laravel naming convention by using Framework instead of Core.
This makes the namespace more descriptive and professional.
"
    
    if git push origin main; then
        echo "   ✓ ${repo_name} atualizado e enviado"
    else
        echo "   ✗ Erro ao enviar ${repo_name}"
        cd - > /dev/null
        return 1
    fi
    
    cd - > /dev/null
    rm -rf "$temp_dir"
    echo ""
}

# Atualiza o pacote principal (alphavel)
echo "📦 Atualizando: alphavel (pacote principal)"
temp_dir="/tmp/alphavel-framework-alphavel"
rm -rf "$temp_dir"
git clone "https://github.com/${GITHUB_ORG}/alphavel.git" "$temp_dir"
cd "$temp_dir" || exit 1

# Remove tudo e copia do monorepo
echo "   Sincronizando com monorepo..."
rm -rf ./*
rm -rf .gitignore
cp -r "${MONOREPO_PATH}/packages/core/src"/* ./

# Cria README.md
cat > README.md << 'EOF'
# Alphavel Framework

Core components of Alphavel Framework - Ultra-fast PHP framework powered by Swoole.

## Features

- ⚡ **Ultra-fast** - Powered by Swoole (520k+ req/s)
- 🏗️ **Modular** - Clean architecture with independent packages
- 📦 **PSR Compliant** - PSR-3, PSR-4, PSR-11
- 🎨 **Auto Facades** - Laravel-style facades with zero configuration
- 🚀 **Modern PHP** - PHP 8.1+ with type safety

## Installation

```bash
composer require alphavel/alphavel
```

## Documentation

Visit [Alphavel Documentation](https://github.com/alphavel) for complete documentation.

## License

MIT License
EOF

# Cria composer.json
cat > composer.json << 'EOF'
{
    "name": "alphavel/alphavel",
    "description": "Core components of Alphavel Framework - Ultra-fast PHP framework powered by Swoole",
    "type": "library",
    "keywords": ["framework", "swoole", "high-performance", "alphavel", "php"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "ext-swoole": "*",
        "psr/container": "^2.0",
        "psr/log": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Framework\\": ""
        },
        "files": [
            "helpers.php"
        ]
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF

git add .
git commit -m "Refactor: Rename Alphavel\\Core to Alphavel\\Framework

Follow Laravel naming convention by using Framework instead of Core.
This makes the namespace more descriptive and professional.

Full source code synchronized from monorepo.
"

git push origin main
echo "   ✓ alphavel atualizado e enviado"
echo ""

cd "$MONOREPO_PATH"
rm -rf "$temp_dir"

# Atualiza os outros pacotes
for repo in cache database events logging validation support; do
    update_repo "$repo" ""
done

# Atualiza skeleton
echo "📦 Atualizando: skeleton"
temp_dir="/tmp/alphavel-framework-skeleton"
rm -rf "$temp_dir"
git clone "https://github.com/${GITHUB_ORG}/skeleton.git" "$temp_dir"
cd "$temp_dir" || exit 1

# Atualiza todos os arquivos PHP
find . -type f -name "*.php" -not -path "./.git/*" | while read -r file; do
    if grep -q "Alphavel\\\\Core\|Alphavel/Core" "$file"; then
        sed -i 's/Alphavel\\Core/Alphavel\\Framework/g' "$file"
        sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' "$file"
    fi
done

# Atualiza composer.json
if [ -f "composer.json" ]; then
    sed -i 's/Alphavel\\\\Core/Alphavel\\\\Framework/g' composer.json
    sed -i 's/Alphavel\/Core/Alphavel\/Framework/g' composer.json
fi

if ! git diff --quiet; then
    git add .
    git commit -m "Refactor: Rename Alphavel\\Core to Alphavel\\Framework"
    git push origin main
    echo "   ✓ skeleton atualizado e enviado"
else
    echo "   ⊘ Nenhuma mudança necessária"
fi

cd "$MONOREPO_PATH"
rm -rf "$temp_dir"
echo ""

echo "========================================"
echo "  Concluído!"
echo "========================================"
echo ""
echo "Todos os repositórios usam agora: Alphavel\\Framework"
echo "Verifique em: https://github.com/orgs/alphavel/repositories"
echo ""
