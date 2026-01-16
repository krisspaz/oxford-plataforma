#!/bin/bash
# ===========================================
# Sistema Oxford - Remove Secrets from Git History
# ===========================================
# CRITICAL: This script rewrites git history!
# Usage: ./01_remove_secrets_from_git.sh
# ===========================================

set -euo pipefail

echo "🔒 Sistema Oxford - Eliminación de Secrets del Historial Git"
echo "============================================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Navigate to repo root
cd "$(git rev-parse --show-toplevel)" || {
    echo -e "${RED}❌ ERROR: No estás en un repositorio git${NC}"
    exit 1
}

# STEP 1: Create backup branch
BACKUP_BRANCH="backup-$(date +%Y%m%d-%H%M)"
echo -e "${YELLOW}📦 Paso 1: Creando backup en rama '${BACKUP_BRANCH}'...${NC}"
git branch "$BACKUP_BRANCH" || {
    echo -e "${RED}❌ ERROR: No se pudo crear backup${NC}"
    exit 1
}
echo -e "${GREEN}✅ Backup creado: $BACKUP_BRANCH${NC}"
echo ""

# STEP 2: Install git-filter-repo if not present
echo -e "${YELLOW}🔧 Paso 2: Verificando git-filter-repo...${NC}"
if ! command -v git-filter-repo &> /dev/null; then
    echo "   Instalando git-filter-repo..."
    if command -v brew &> /dev/null; then
        brew install git-filter-repo
    elif command -v pip3 &> /dev/null; then
        pip3 install git-filter-repo
    else
        echo -e "${RED}❌ ERROR: No se pudo instalar git-filter-repo${NC}"
        echo "   Instala manualmente: brew install git-filter-repo"
        exit 1
    fi
fi
echo -e "${GREEN}✅ git-filter-repo disponible${NC}"
echo ""

# STEP 3: List files to remove
echo -e "${YELLOW}🗂️  Paso 3: Archivos a eliminar del historial:${NC}"
FILES_TO_REMOVE=(
    ".env"
    "backend/symfony/.env"
    "backend/ai_service/.env"
    "frontend/.env"
    ".env.local"
    "backend/symfony/.env.local"
    "backend/ai_service/.env.local"
)

for file in "${FILES_TO_REMOVE[@]}"; do
    if git log --all --full-history -- "$file" 2>/dev/null | grep -q "commit"; then
        echo "   ⚠️  $file (ENCONTRADO en historial)"
    else
        echo "   ✓  $file (no en historial)"
    fi
done
echo ""

# STEP 4: Confirm action
echo -e "${RED}⚠️  ADVERTENCIA: Esta acción es IRREVERSIBLE${NC}"
echo "   - El historial de git será reescrito"
echo "   - Todos los colaboradores deberán re-clonar"
echo "   - Los hashes de commits cambiarán"
echo ""
read -p "¿Continuar? (escribe 'SI' para confirmar): " CONFIRM

if [ "$CONFIRM" != "SI" ]; then
    echo "❌ Operación cancelada"
    exit 0
fi
echo ""

# STEP 5: Remove files from history
echo -e "${YELLOW}🔥 Paso 4: Eliminando archivos del historial...${NC}"
for file in "${FILES_TO_REMOVE[@]}"; do
    echo "   Procesando: $file"
    git filter-repo --invert-paths --path "$file" --force 2>/dev/null || true
done
echo -e "${GREEN}✅ Archivos eliminados del historial${NC}"
echo ""

# STEP 6: Verification
echo -e "${YELLOW}🔍 Paso 5: Verificando limpieza...${NC}"
FOUND=0
for file in "${FILES_TO_REMOVE[@]}"; do
    if git log --all --full-history -- "$file" 2>/dev/null | grep -q "commit"; then
        echo -e "${RED}   ❌ $file todavía en historial${NC}"
        FOUND=1
    fi
done

if [ "$FOUND" -eq 0 ]; then
    echo -e "${GREEN}✅ Verificación exitosa: No hay .env en el historial${NC}"
else
    echo -e "${RED}⚠️  Algunos archivos no se eliminaron completamente${NC}"
fi
echo ""

# STEP 7: Instructions for push
echo -e "${YELLOW}📤 Paso 6: Para subir los cambios:${NC}"
echo ""
echo "   git push origin main --force-with-lease"
echo ""
echo -e "${RED}⚠️  IMPORTANTE después del push:${NC}"
echo "   1. Todos los colaboradores deben re-clonar"
echo "   2. Regenera TODOS los secrets (02_generate_new_secrets.sh)"
echo "   3. Regenera las JWT keys (03_regenerate_jwt_keys.sh)"
echo "   4. Actualiza los secrets en CI/CD y producción"
echo ""
echo -e "${GREEN}✅ Limpieza de historial completada${NC}"
