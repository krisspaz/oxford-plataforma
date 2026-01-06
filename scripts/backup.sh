#!/bin/bash
# Backup script for Oxford Plataforma
# Creates database and file backups

set -e

BACKUP_DIR="/backups/oxford"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

echo "🗄️ Starting backup at $(date)"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# ==========================================
# DATABASE BACKUP
# ==========================================

echo "📊 Backing up database..."

DB_BACKUP_FILE="$BACKUP_DIR/db_backup_$DATE.sql.gz"

# PostgreSQL backup
pg_dump "$DATABASE_URL" | gzip > "$DB_BACKUP_FILE"

echo "✅ Database backed up to $DB_BACKUP_FILE"

# ==========================================
# FILES BACKUP
# ==========================================

echo "📁 Backing up files..."

FILES_BACKUP="$BACKUP_DIR/files_backup_$DATE.tar.gz"

# Backup uploads and config
tar -czf "$FILES_BACKUP" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='var/cache' \
    --exclude='var/log' \
    ./backend/symfony/public/uploads \
    ./backend/symfony/.env \
    ./backend/ai_service/models \
    2>/dev/null || true

echo "✅ Files backed up to $FILES_BACKUP"

# ==========================================
# CLEANUP OLD BACKUPS
# ==========================================

echo "🧹 Cleaning old backups..."

find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete

echo "✅ Old backups cleaned"

# ==========================================
# UPLOAD TO S3 (optional)
# ==========================================

if [ -n "$AWS_S3_BUCKET" ]; then
    echo "☁️ Uploading to S3..."
    aws s3 cp "$DB_BACKUP_FILE" "s3://$AWS_S3_BUCKET/backups/"
    aws s3 cp "$FILES_BACKUP" "s3://$AWS_S3_BUCKET/backups/"
    echo "✅ Uploaded to S3"
fi

# ==========================================
# SUMMARY
# ==========================================

echo ""
echo "🎉 Backup complete!"
echo "   Database: $DB_BACKUP_FILE ($(du -h "$DB_BACKUP_FILE" | cut -f1))"
echo "   Files: $FILES_BACKUP ($(du -h "$FILES_BACKUP" | cut -f1))"
echo "   Retention: $RETENTION_DAYS days"
