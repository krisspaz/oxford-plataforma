#!/bin/bash
# ===========================================
# Sistema Oxford - Remove Duplicate Files
# ===========================================
# Removes all duplicate files/folders created by macOS
# Pattern: "filename 2.ext", "folder 3/", etc.
# ===========================================

set -euo pipefail

echo "🧹 Sistema Oxford - Limpieza de Duplicados"
echo "==========================================="
echo ""

# Navigate to repo root
cd "$(git rev-parse --show-toplevel)" || {
    echo "❌ ERROR: No estás en un repositorio git"
    exit 1
}

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Count before
echo -e "${YELLOW}📊 Contando archivos antes de limpieza...${NC}"
BEFORE_FILES=$(find . -type f | wc -l | tr -d ' ')
BEFORE_DIRS=$(find . -type d | wc -l | tr -d ' ')
echo "   Archivos: $BEFORE_FILES"
echo "   Carpetas: $BEFORE_DIRS"
echo ""

# Find duplicates
echo -e "${YELLOW}🔍 Buscando duplicados...${NC}"
echo ""

# Arrays to store findings
declare -a DUPLICATE_FILES=()
declare -a DUPLICATE_DIRS=()

# Find duplicate files (pattern: "name 2.ext", "name 3.ext", etc.)
while IFS= read -r -d '' file; do
    DUPLICATE_FILES+=("$file")
done < <(find . -type f \( -name "* 2.*" -o -name "* 3.*" -o -name "* 4.*" -o -name "* 2" -o -name "* 3" -o -name "* 4" \) -print0 2>/dev/null)

# Find duplicate directories
while IFS= read -r -d '' dir; do
    DUPLICATE_DIRS+=("$dir")
done < <(find . -type d \( -name "* 2" -o -name "* 3" -o -name "* 4" \) -print0 2>/dev/null)

# Report findings
echo -e "${YELLOW}📋 Duplicados encontrados:${NC}"
echo ""

if [ ${#DUPLICATE_FILES[@]} -eq 0 ] && [ ${#DUPLICATE_DIRS[@]} -eq 0 ]; then
    echo -e "${GREEN}✅ No se encontraron archivos duplicados${NC}"
    exit 0
fi

echo "   Archivos: ${#DUPLICATE_FILES[@]}"
for file in "${DUPLICATE_FILES[@]}"; do
    echo "      - $file"
done

echo ""
echo "   Carpetas: ${#DUPLICATE_DIRS[@]}"
for dir in "${DUPLICATE_DIRS[@]}"; do
    echo "      - $dir"
done
echo ""

# Confirm deletion
read -p "¿Eliminar todos los duplicados? (s/n): " CONFIRM
if [[ ! "$CONFIRM" =~ ^[sS]$ ]]; then
    echo "❌ Operación cancelada"
    exit 0
fi

# Delete duplicates
echo ""
echo -e "${YELLOW}🗑️  Eliminando duplicados...${NC}"

DELETED_COUNT=0

# Delete duplicate directories first (must be done in reverse depth order)
for dir in "${DUPLICATE_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        rm -rf "$dir"
        echo "   ✓ Eliminado: $dir"
        ((DELETED_COUNT++))
    fi
done

# Delete duplicate files
for file in "${DUPLICATE_FILES[@]}"; do
    if [ -f "$file" ]; then
        rm -f "$file"
        echo "   ✓ Eliminado: $file"
        ((DELETED_COUNT++))
    fi
done

# Count after
echo ""
echo -e "${YELLOW}📊 Contando archivos después de limpieza...${NC}"
AFTER_FILES=$(find . -type f | wc -l | tr -d ' ')
AFTER_DIRS=$(find . -type d | wc -l | tr -d ' ')
echo "   Archivos: $AFTER_FILES (antes: $BEFORE_FILES)"
echo "   Carpetas: $AFTER_DIRS (antes: $BEFORE_DIRS)"
echo ""

# Create git commit
echo -e "${YELLOW}📝 Creando commit...${NC}"
git add -A
git commit -m "chore: remove $DELETED_COUNT duplicate files and folders

Removed files matching patterns:
- '* 2.*', '* 3.*', '* 4.*' (duplicate files)
- '* 2/', '* 3/', '* 4/' (duplicate folders)

Before: $BEFORE_FILES files, $BEFORE_DIRS dirs
After:  $AFTER_FILES files, $AFTER_DIRS dirs" || echo "   (No hay cambios para commit)"

echo ""
echo -e "${GREEN}✅ Limpieza completada: $DELETED_COUNT elementos eliminados${NC}"
