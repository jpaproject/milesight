FROM nginx:1.27.5-alpine3.21

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Create nginx user
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy static assets (for production)
COPY --chown=www:www public /var/www/public

# Set proper permissions
RUN chown -R www:www /var/cache/nginx && \
    chown -R www:www /var/log/nginx && \
    chown -R www:www /etc/nginx/conf.d

# Switch to non-root user
USER www

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
