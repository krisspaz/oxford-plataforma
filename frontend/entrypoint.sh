#!/bin/sh
set -e

echo "Starting Frontend Entrypoint..."
echo "Current PORT: $PORT"

# Replace $PORT in Nginx config
# We match 'listen 80;' specifically because that is what is in our nginx.conf
echo "Injecting PORT into Nginx config..."
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/conf.d/default.conf

echo "Configuration updated. Starting Nginx..."
exec nginx -g 'daemon off;'
