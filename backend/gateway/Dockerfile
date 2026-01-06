FROM nginx:alpine

# Remove default config
RUN rm /etc/nginx/conf.d/default.conf

# Copy custom config
# Note: copying to nginx.conf (main) or conf.d/default.conf depending on structure.
# Our nginx.conf includes "http" block, so it should replace /etc/nginx/nginx.conf 
# OR we should adjust it to be a partial.
# The user provided a full config with "events" and "http", so we replace nginx.conf.
COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
