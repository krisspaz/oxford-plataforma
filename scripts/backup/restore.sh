#!/bin/bash
# ====================================
# Sistema Oxford - Database Restore Script
# ====================================
# Usage: ./restore.sh /path/to/backup.sql.gz

set -e

# Configuration
POSTGRES_HOST="${POSTGRES_HOST:-localhost}"
POSTGRES_PORT="${POSTGRES_PORT:-5433}"
POSTGRES_DB="${POSTGRES_DB:-oxford_db}"
POSTGRES_USER="${POSTGRES_USER:-oxford_user}"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check arguments
if [ -z "$1" ]; then
    echo -e "${RED}Usage: $0 /path/to/backup.sql.gz${NC}"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "${BACKUP_FILE}" ]; then
    echo -e "${RED}ERROR: Backup file not found: ${BACKUP_FILE}${NC}"
    exit 1
fi

echo "======================================"
echo "Starting Oxford Database Restore"
echo "Timestamp: $(date)"
echo "Backup file: ${BACKUP_FILE}"
echo "======================================"

# Confirm restore
read -p "This will OVERWRITE the current database. Are you sure? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Restore cancelled."
    exit 0
fi

# Check if psql is available
if ! command -v psql &> /dev/null; then
    echo -e "${RED}ERROR: psql is not installed${NC}"
    exit 1
fi

# Perform restore
echo -e "${YELLOW}Restoring database...${NC}"

if [[ "${BACKUP_FILE}" == *.gz ]]; then
    # Compressed backup
    gunzip -c "${BACKUP_FILE}" | PGPASSWORD="${POSTGRES_PASSWORD}" psql \
        -h "${POSTGRES_HOST}" \
        -p "${POSTGRES_PORT}" \
        -U "${POSTGRES_USER}" \
        -d "${POSTGRES_DB}" \
        --quiet
else
    # Uncompressed backup
    PGPASSWORD="${POSTGRES_PASSWORD}" psql \
        -h "${POSTGRES_HOST}" \
        -p "${POSTGRES_PORT}" \
        -U "${POSTGRES_USER}" \
        -d "${POSTGRES_DB}" \
        --quiet \
        < "${BACKUP_FILE}"
fi

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Database restored successfully!${NC}"
else
    echo -e "${RED}ERROR: Restore failed!${NC}"
    exit 1
fi

echo "======================================"
echo -e "${GREEN}Restore completed!${NC}"
echo "======================================"
