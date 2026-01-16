#!/bin/bash
# ===========================================
# Sistema Oxford - Secret Generator
# ===========================================
# Generates secure secrets for all environments
# Usage: ./02_generate_new_secrets.sh

set -e

echo "🔐 Generando nuevos secrets seguros..."

# Generate random strings
generate_secret() {
    local length=$1
    openssl rand -base64 $((length * 2)) | tr -dc 'a-zA-Z0-9' | head -c "$length"
}

generate_hex() {
    local length=$1
    openssl rand -hex "$((length / 2))"
}

# Generate all secrets
POSTGRES_PASSWORD=$(generate_secret 32)
APP_SECRET=$(generate_hex 64)
JWT_PASSPHRASE=$(generate_secret 32)
AI_SECRET_KEY=$(generate_hex 64)
MERCURE_SECRET=$(generate_hex 32)
MINIO_PASSWORD=$(generate_secret 24)
MONGO_PASSWORD=$(generate_secret 24)

# Create output file
OUTPUT_FILE=".env.generated"

cat > "$OUTPUT_FILE" << EOF
# ========================================
# GENERATED SECRETS - $(date +%Y-%m-%d)
# ========================================
# IMPORTANT: Copy these to your .env.local files
# DO NOT commit this file!
# ========================================

# Database
POSTGRES_PASSWORD=$POSTGRES_PASSWORD

# Symfony
APP_SECRET=$APP_SECRET
JWT_PASSPHRASE=$JWT_PASSPHRASE

# AI Service
AI_SECRET_KEY=$AI_SECRET_KEY

# Mercure
MERCURE_JWT_SECRET=$MERCURE_SECRET

# MinIO
MINIO_ROOT_PASSWORD=$MINIO_PASSWORD

# MongoDB
MONGO_PASSWORD=$MONGO_PASSWORD

# ========================================
# DEPLOYMENT INSTRUCTIONS:
# ========================================
# 1. Copy relevant secrets to each .env.local:
#    - Root .env.local: POSTGRES_PASSWORD
#    - backend/symfony/.env.local: APP_SECRET, JWT_PASSPHRASE
#    - backend/ai_service/.env.local: AI_SECRET_KEY
#
# 2. Regenerate JWT keys:
#    cd backend/symfony
#    php bin/console lexik:jwt:generate-keypair --overwrite
#
# 3. Delete this file after copying secrets:
#    rm .env.generated
# ========================================
EOF

echo ""
echo "✅ Secrets generados en: $OUTPUT_FILE"
echo ""
echo "📋 Resumen:"
echo "   POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:0:8}..."
echo "   APP_SECRET: ${APP_SECRET:0:16}..."
echo "   JWT_PASSPHRASE: ${JWT_PASSPHRASE:0:8}..."
echo "   AI_SECRET_KEY: ${AI_SECRET_KEY:0:16}..."
echo ""
echo "⚠️  IMPORTANTE: No commits este archivo!"
echo "    Agrega '.env.generated' a .gitignore"
