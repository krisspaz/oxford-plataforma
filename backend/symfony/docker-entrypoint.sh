#!/bin/bash
set -e

# Configure Apache to listen on Railway's PORT
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/g" /etc/apache2/sites-enabled/000-default.conf
fi

# Run migrations if database is ready
if [ -n "$DATABASE_URL" ]; then
    echo "Running migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true
fi

# Create keys if not exist
if [ ! -f config/jwt/private.pem ]; then
    echo "Generating JWT keys..."
    php bin/console lexik:jwt:generate-keypair --skip-if-exists
    # Fix permissions
    chown www-data:www-data config/jwt/*.pem
fi

# Clear cache
php bin/console cache:clear

# Execute Command
exec "$@"
