FROM nginx:alpine

# Install dos2unix
RUN apk add --no-cache dos2unix

# Copy custom config
COPY backend/gateway/nginx.conf /etc/nginx/nginx.conf

# Copy entrypoint
COPY backend/gateway/entrypoint.sh /docker-entrypoint.sh
RUN dos2unix /docker-entrypoint.sh && chmod +x /docker-entrypoint.sh

CMD ["/docker-entrypoint.sh"]
