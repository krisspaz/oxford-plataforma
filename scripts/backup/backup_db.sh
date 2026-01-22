#!/bin/bash
set -e

# Configuration
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/oxford"
DB_NAME="${POSTGRES_DB:-oxford_db}"
DB_USER="${POSTGRES_USER:-oxford_user}"
RETENTION_DAYS=7

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
echo "🔄 Creating backup: oxford_$DATE.sql.gz"
# Note: Using interactive docker exec might need -T in scripts, but typically standard exec works without -it
docker exec postgres pg_dump -U $DB_USER -d $DB_NAME | gzip > $BACKUP_DIR/oxford_$DATE.sql.gz

# Verify backup
if [ -f "$BACKUP_DIR/oxford_$DATE.sql.gz" ]; then
    SIZE=$(du -h "$BACKUP_DIR/oxford_$DATE.sql.gz" | cut -f1)
    echo "✅ Backup created successfully ($SIZE)"
else
    echo "❌ Backup failed!"
    exit 1
fi

# Cleanup old backups
echo "🧹 Cleaning backups older than $RETENTION_DAYS days..."
find $BACKUP_DIR -name "oxford_*.sql.gz" -mtime +$RETENTION_DAYS -delete

# List remaining backups
echo "📦 Current backups:"
ls -lh $BACKUP_DIR/oxford_*.sql.gz

# Optional: Upload to S3 (uncomment if needed)
# aws s3 cp $BACKUP_DIR/oxford_$DATE.sql.gz s3://oxford-backups/
