#!/bin/bash
# ====================================
# Sistema Oxford - Database Backup Script
# ====================================
# Run this script daily via cron to backup the database
# Example cron: 0 2 * * * /path/to/backup.sh >> /var/log/oxford-backup.log 2>&1

set -e

# Configuration
BACKUP_DIR="${BACKUP_DIR:-/var/backups/oxford}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"
POSTGRES_HOST="${POSTGRES_HOST:-localhost}"
POSTGRES_PORT="${POSTGRES_PORT:-5433}"
POSTGRES_DB="${POSTGRES_DB:-oxford_db}"
POSTGRES_USER="${POSTGRES_USER:-oxford_user}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/oxford_backup_${TIMESTAMP}.sql.gz"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "======================================"
echo "Starting Oxford Database Backup"
echo "Timestamp: $(date)"
echo "======================================"

# Create backup directory if it doesn't exist
mkdir -p "${BACKUP_DIR}"

# Check if pg_dump is available
if ! command -v pg_dump &> /dev/null; then
    echo -e "${RED}ERROR: pg_dump is not installed${NC}"
    exit 1
fi

# Perform backup
echo -e "${YELLOW}Creating backup...${NC}"
PGPASSWORD="${POSTGRES_PASSWORD}" pg_dump \
    -h "${POSTGRES_HOST}" \
    -p "${POSTGRES_PORT}" \
    -U "${POSTGRES_USER}" \
    -d "${POSTGRES_DB}" \
    --no-owner \
    --no-acl \
    --clean \
    --if-exists \
    | gzip > "${BACKUP_FILE}"

# Check if backup was successful
if [ $? -eq 0 ] && [ -f "${BACKUP_FILE}" ]; then
    BACKUP_SIZE=$(du -h "${BACKUP_FILE}" | cut -f1)
    echo -e "${GREEN}Backup created successfully: ${BACKUP_FILE}${NC}"
    echo "Backup size: ${BACKUP_SIZE}"
else
    echo -e "${RED}ERROR: Backup failed!${NC}"
    exit 1
fi

# Delete old backups
echo -e "${YELLOW}Cleaning up old backups (older than ${RETENTION_DAYS} days)...${NC}"
find "${BACKUP_DIR}" -name "oxford_backup_*.sql.gz" -type f -mtime +${RETENTION_DAYS} -delete
REMAINING=$(ls -1 "${BACKUP_DIR}"/oxford_backup_*.sql.gz 2>/dev/null | wc -l)
echo "Remaining backups: ${REMAINING}"

# Optional: Upload to S3 (uncomment and configure if needed)
# if command -v aws &> /dev/null; then
#     echo "Uploading to S3..."
#     aws s3 cp "${BACKUP_FILE}" "s3://${S3_BUCKET}/backups/"
# fi

echo "======================================"
echo -e "${GREEN}Backup completed successfully!${NC}"
echo "======================================"
