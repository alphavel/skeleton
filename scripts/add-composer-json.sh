#!/bin/bash

# Script para adicionar composer.json a cada repositório
GITHUB_ORG="alphavel"

echo "========================================"
echo "  Adicionando composer.json aos pacotes"
echo "========================================"
echo ""

add_composer_json() {
    local repo_name=$1
    local temp_dir="/tmp/alphavel-update-${repo_name}"
    
    echo "📦 Atualizando: ${repo_name}"
    
    rm -rf "$temp_dir"
    git clone "https://github.com/${GITHUB_ORG}/${repo_name}.git" "$temp_dir"
    cd "$temp_dir" || return 1
    
    # Cria composer.json apropriado
    case $repo_name in
        "core")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/core",
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
            "Alphavel\\Core\\": ""
        },
        "files": [
            "helpers.php"
        ]
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
        "cache")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/cache",
    "description": "Cache package for Alphavel Framework",
    "type": "library",
    "keywords": ["cache", "redis", "memcached", "alphavel"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Cache\\": ""
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Cache\\CacheServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
        "database")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/database",
    "description": "Database package for Alphavel Framework",
    "type": "library",
    "keywords": ["database", "pdo", "query-builder", "alphavel"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "ext-pdo": "*",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Database\\": ""
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Database\\DatabaseServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
        "events")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/events",
    "description": "Event dispatcher for Alphavel Framework",
    "type": "library",
    "keywords": ["events", "observer", "dispatcher", "alphavel"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Events\\": ""
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Events\\EventServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
        "logging")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/logging",
    "description": "PSR-3 compliant logging package for Alphavel Framework",
    "type": "library",
    "keywords": ["logging", "psr-3", "logger", "alphavel"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "psr/log": "^3.0",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Logging\\": ""
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Logging\\LoggingServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
        "validation")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/validation",
    "description": "Validation package for Alphavel Framework",
    "type": "library",
    "keywords": ["validation", "validator", "alphavel"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Validation\\": ""
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Validation\\ValidationServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
        "support")
            cat > composer.json << 'EOF'
{
    "name": "alphavel/support",
    "description": "Support utilities for Alphavel Framework",
    "type": "library",
    "keywords": ["utilities", "helpers", "collection", "alphavel"],
    "license": "MIT",
    "authors": [
        {
            "name": "Arthur Weber",
            "email": "arthur@alphavel.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "alphavel/core": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Alphavel\\Support\\": ""
        }
    },
    "extra": {
        "alphavel": {
            "providers": [
                "Alphavel\\Support\\SupportServiceProvider"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
            ;;
    esac
    
    # Adiciona README.md se não existir
    if [ ! -f "README.md" ]; then
        cat > README.md << EOF
# Alphavel ${repo_name^}

${repo_name^} package for Alphavel Framework.

## Installation

\`\`\`bash
composer require alphavel/${repo_name}
\`\`\`

## Documentation

Visit [Alphavel Documentation](https://github.com/alphavel) for complete documentation.

## License

MIT License
EOF
    fi
    
    git add .
    git commit -m "Add composer.json and README"
    git push origin main
    
    cd - > /dev/null
    rm -rf "$temp_dir"
    
    echo "   ✓ ${repo_name} atualizado"
    echo ""
}

# Atualiza cada pacote
for repo in core cache database events logging validation support; do
    add_composer_json "$repo"
done

echo "========================================"
echo "  Concluído!"
echo "========================================"
echo ""
echo "Próximos passos:"
echo "1. Verifique os repositórios em: https://github.com/orgs/alphavel/repositories"
echo "2. Submeta cada pacote no Packagist: https://packagist.org/packages/submit"
echo "3. Após aprovação, instale com: composer require alphavel/core"
echo ""
