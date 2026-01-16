#!/bin/bash
# ===========================================
# Sistema Oxford - 10/10 Verification Script
# ===========================================
# Verifies all requirements for 10/10 rating
# Exit code: 0 = PASS, 1 = FAIL
# ===========================================

set -euo pipefail

echo "🔍 Sistema Oxford - Verificación 10/10"
echo "========================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Navigate to repo root
cd "$(git rev-parse --show-toplevel)" || exit 1

PASS=0
FAIL=0
WARN=0

check_pass() {
    echo -e "${GREEN}✅ PASS${NC}: $1"
    ((PASS++))
}

check_fail() {
    echo -e "${RED}❌ FAIL${NC}: $1"
    ((FAIL++))
}

check_warn() {
    echo -e "${YELLOW}⚠️  WARN${NC}: $1"
    ((WARN++))
}

echo "1️⃣  Checking secrets in git history..."
if git log --all --full-history -- "*.env" 2>/dev/null | grep -q "commit"; then
    check_fail ".env files found in git history"
else
    check_pass "No .env files in git history"
fi

echo ""
echo "2️⃣  Checking duplicate files..."
DUPLICATES=$(find . -type f \( -name "* 2.*" -o -name "* 3.*" -o -name "* 4.*" \) 2>/dev/null | wc -l | tr -d ' ')
if [ "$DUPLICATES" -gt 0 ]; then
    check_fail "Found $DUPLICATES duplicate files"
else
    check_pass "No duplicate files"
fi

echo ""
echo "3️⃣  Checking .env.example files..."
ENVS_OK=true
for file in ".env.example" "backend/symfony/.env.example" "backend/ai_service/.env.example" "frontend/.env.example"; do
    if [ -f "$file" ]; then
        echo "   ✓ $file"
    else
        echo "   ✗ $file MISSING"
        ENVS_OK=false
    fi
done
if [ "$ENVS_OK" = true ]; then
    check_pass "All .env.example files exist"
else
    check_fail "Missing .env.example files"
fi

echo ""
echo "4️⃣  Checking React Query..."
if grep -q "@tanstack/react-query" frontend/package.json 2>/dev/null; then
    check_pass "React Query installed"
else
    check_fail "React Query not installed"
fi

echo ""
echo "5️⃣  Checking Sonner toast..."
if grep -q '"sonner"' frontend/package.json 2>/dev/null; then
    check_pass "Sonner installed"
else
    check_fail "Sonner not installed"
fi

echo ""
echo "6️⃣  Checking alert() usage..."
ALERTS=$(grep -r "alert(" frontend/src --include="*.jsx" --include="*.js" 2>/dev/null | wc -l | tr -d ' ')
if [ "$ALERTS" -lt 10 ]; then
    check_pass "Minimal alert() usage ($ALERTS found)"
else
    check_warn "Still $ALERTS alert() calls - consider migrating to toast"
fi

echo ""
echo "7️⃣  Checking mobile documentation..."
if grep -q "Aplicaciones Móviles" README.md 2>/dev/null; then
    check_pass "Mobile apps documented in README"
else
    check_fail "Mobile apps not documented"
fi

echo ""
echo "8️⃣  Checking security scripts..."
SCRIPTS_OK=true
for script in "scripts/security/01_remove_secrets_from_git.sh" "scripts/security/02_generate_new_secrets.sh" "scripts/security/03_regenerate_jwt_keys.sh"; do
    if [ -f "$script" ] && [ -x "$script" ]; then
        echo "   ✓ $script"
    else
        echo "   ✗ $script MISSING or not executable"
        SCRIPTS_OK=false
    fi
done
if [ "$SCRIPTS_OK" = true ]; then
    check_pass "All security scripts exist and are executable"
else
    check_fail "Missing security scripts"
fi

echo ""
echo "9️⃣  Checking docker-compose.prod.yml..."
if grep -q "\${POSTGRES_PASSWORD:?" infrastructure/docker-compose.prod.yml 2>/dev/null; then
    check_pass "docker-compose.prod.yml uses env var validation"
else
    check_fail "docker-compose.prod.yml may have hardcoded values"
fi

echo ""
echo "🔟 Checking .gitignore..."
if grep -q ".env.generated" .gitignore 2>/dev/null; then
    check_pass ".gitignore includes .env.generated"
else
    check_warn ".gitignore missing .env.generated"
fi

echo ""
echo "========================================"
echo "📊 RESULTS"
echo "========================================"
echo -e "   ${GREEN}PASS${NC}: $PASS"
echo -e "   ${RED}FAIL${NC}: $FAIL"
echo -e "   ${YELLOW}WARN${NC}: $WARN"
echo ""

if [ "$FAIL" -eq 0 ]; then
    echo -e "${GREEN}✨ VERIFICACIÓN EXITOSA - READY FOR 10/10 ✨${NC}"
    exit 0
else
    echo -e "${RED}❌ VERIFICACIÓN FALLIDA - $FAIL checks failed${NC}"
    exit 1
fi
