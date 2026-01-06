FROM nginx:alpine

# Copy custom config
COPY backend/gateway/nginx.conf /etc/nginx/nginx.conf

# Copy entrypoint
COPY backend/gateway/entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

CMD ["/docker-entrypoint.sh"]
