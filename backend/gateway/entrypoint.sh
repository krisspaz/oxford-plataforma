#!/bin/sh
set -e

# Replace $PORT for the Gateway itself
echo "Configuring Gateway to listen on port $PORT..."
# Robust regex: matches "listen" + spaces + any digits + ";"
sed -i -E "s/listen[[:space:]]+[0-9]+;/listen $PORT;/g" /etc/nginx/nginx.conf

# OPTIONAL: If we needed dynamic upstream ports, we could do it here too.
# For now, assuming standard naming/defaults or Render's internal mesh.
# If upstreams fail, we might need to inject env vars.

# Execute Nginx
echo "Starting Nginx Gateway..."
exec nginx -g 'daemon off;'
