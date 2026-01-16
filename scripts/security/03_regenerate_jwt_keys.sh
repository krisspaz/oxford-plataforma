#!/bin/bash
# ===========================================
# Sistema Oxford - JWT Keys Regeneration
# ===========================================
# Regenerates JWT key pair for authentication
# Usage: ./03_regenerate_jwt_keys.sh

set -e

echo "🔑 Regenerando claves JWT..."

# Navigate to Symfony directory
cd "$(dirname "$0")/../../backend/symfony"

# Check if we have a passphrase
if [ -z "$JWT_PASSPHRASE" ]; then
    echo "⚠️  JWT_PASSPHRASE no definido"
    echo "   Usando passphrase de .env.local si existe..."
    
    if [ -f ".env.local" ]; then
        source .env.local
    fi
fi

# Create jwt directory if it doesn't exist
mkdir -p config/jwt

# Backup existing keys if they exist
if [ -f "config/jwt/private.pem" ]; then
    echo "📦 Respaldando claves existentes..."
    mv config/jwt/private.pem config/jwt/private.pem.backup.$(date +%s)
    mv config/jwt/public.pem config/jwt/public.pem.backup.$(date +%s)
fi

# Generate new keys using Symfony command
echo "🔐 Generando nuevas claves RSA 4096-bit..."
php bin/console lexik:jwt:generate-keypair --overwrite

# Set correct permissions
chmod 600 config/jwt/private.pem
chmod 644 config/jwt/public.pem

echo ""
echo "✅ Claves JWT regeneradas exitosamente!"
echo ""
echo "📁 Ubicación:"
echo "   Private: config/jwt/private.pem (600)"
echo "   Public:  config/jwt/public.pem (644)"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   1. Los tokens JWT existentes serán invalidados"
echo "   2. Los usuarios deberán iniciar sesión nuevamente"
echo "   3. No commits las claves .pem a git"
