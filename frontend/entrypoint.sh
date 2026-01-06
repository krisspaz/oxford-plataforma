#!/bin/sh
set -e

# Replace $PORT in Nginx config
echo "Configuring Nginx to listen on port $PORT..."
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/conf.d/default.conf

# Execute Nginx
echo "Starting Nginx..."
exec nginx -g 'daemon off;'
