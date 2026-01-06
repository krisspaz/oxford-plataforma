#!/bin/bash
set -e

# Render assigns a dynamic PORT (e.g. 10000)
# Apache listens on 80 by default. We must update ports.conf
if [ -n "$PORT" ]; then
    echo "Updating Apache to listen on port $PORT..."
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/g" /etc/apache2/sites-enabled/000-default.conf
fi

exec "$@"
