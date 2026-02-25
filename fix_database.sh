#!/bin/bash
echo "🔧 Reparando Base de Datos Oxford..."

# 1. Identificar el contenedor de base de datos
DB_CONTAINER=$(docker ps -qf "name=database")

if [ -z "$DB_CONTAINER" ]; then
    echo "❌ Error: No se encontró el contenedor de base de datos corriendo."
    echo "Ejecuta 'docker-compose up -d database' primero."
    exit 1
fi

echo "✅ Contenedor encontrado: $DB_CONTAINER"

# 2. Copiar archivos semilla
echo "📂 Copiando archivos semilla..."
docker cp seed_essentials.sql $DB_CONTAINER:/tmp/01_essentials.sql
docker cp seed_admin.sql $DB_CONTAINER:/tmp/02_admin.sql
docker cp seed_users.sql $DB_CONTAINER:/tmp/03_users.sql

# 3. Ejecutar SQL
echo "🚀 Ejecutando Seeds..."

# Password hardcoded from docker-compose.yml for repair convenience
# In a real script we might prompt or read .env
DB_USER="oxford_user"
DB_PASS="oxford2026"
DB_NAME="oxford_db"

docker exec $DB_CONTAINER sh -c "mysql -u$DB_USER -p$DB_PASS $DB_NAME < /tmp/01_essentials.sql"
echo "  - Essentials cargados."

docker exec $DB_CONTAINER sh -c "mysql -u$DB_USER -p$DB_PASS $DB_NAME < /tmp/02_admin.sql"
echo "  - Admin cargado."

docker exec $DB_CONTAINER sh -c "mysql -u$DB_USER -p$DB_PASS $DB_NAME < /tmp/03_users.sql"
echo "  - Usuarios y Staff cargados."

echo "✅ Base de datos reparada exitosamente."
echo "   Usuario: admin@oxford.edu"
echo "   Pass: oxford123"
