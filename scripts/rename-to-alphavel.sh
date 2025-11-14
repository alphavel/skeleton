#!/bin/bash

###############################################################################
# Script: Renomear Alphavel → Alphavel
# Descrição: Substitui todas as referências ao nome Alphavel por Alphavel
###############################################################################

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  Renaming: Alphavel → Alphavel${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

# Contador
COUNT=0

# Função para substituir em um arquivo
replace_in_file() {
    local file="$1"
    
    if grep -q "Alphavel\|alphavel" "$file" 2>/dev/null; then
        echo -n "Processing: $file ... "
        
        # Substituir namespace Alphavel\
        sed -i 's/Alphavel\\/Alphavel\\/g' "$file"
        
        # Substituir alphavel/ em URLs e paths
        sed -i 's/alphavel\//alphavel\//g' "$file"
        
        # Substituir alphavel standalone (mas não em palavras compostas)
        sed -i 's/\bpfast\b/alphavel/g' "$file"
        
        # Substituir Alphavel standalone
        sed -i 's/\bAlphavel\b/Alphavel/g' "$file"
        
        # Casos especiais em strings
        sed -i "s/'alphavel'/'alphavel'/g" "$file"
        sed -i 's/"alphavel"/"alphavel"/g' "$file"
        
        echo -e "${GREEN}✓${NC}"
        COUNT=$((COUNT + 1))
    fi
}

# Arquivos PHP
echo -e "${YELLOW}PHP files:${NC}"
find . -type f -name "*.php" ! -path "./vendor/*" ! -path "./.git/*" | while read file; do
    replace_in_file "$file"
done
echo ""

# Arquivos JSON
echo -e "${YELLOW}JSON files:${NC}"
find . -type f -name "*.json" ! -path "./vendor/*" ! -path "./.git/*" | while read file; do
    replace_in_file "$file"
done
echo ""

# Arquivos MD
echo -e "${YELLOW}Markdown files:${NC}"
find . -type f -name "*.md" ! -path "./vendor/*" ! -path "./.git/*" | while read file; do
    replace_in_file "$file"
done
echo ""

# Arquivos de config
echo -e "${YELLOW}Config files:${NC}"
for file in config/*.php bootstrap/*.php public/*.php; do
    if [ -f "$file" ]; then
        replace_in_file "$file"
    fi
done
echo ""

# Scripts
echo -e "${YELLOW}Scripts:${NC}"
if [ -d "scripts" ]; then
    find scripts -type f -name "*.sh" | while read file; do
        replace_in_file "$file"
    done
fi
echo ""

echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}  ✓ Renaming complete!${NC}"
echo -e "${GREEN}  Total files processed: $COUNT${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo "Next steps:"
echo "  1. Review changes: git diff"
echo "  2. Test application: php -S localhost:8000 -t public/"
echo "  3. Regenerate facades: php generate-facades.php"
echo "  4. Update repository: mv alphavel /path/to/new/location"
echo ""
