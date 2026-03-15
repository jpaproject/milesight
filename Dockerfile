# PHP Base Image
# [NOTE] Change PHP version here if needed (e.g., php:8.2-fpm-alpine).
FROM php:8.3.21-fpm-alpine3.20 AS base

# Update repositories
RUN apk update

RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    libxml2-dev \
    oniguruma-dev \
    libzip-dev \
    icu-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo \
    # [NOTE] For MySQL, use `pdo_mysql` instead of `pdo_pgsql`
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www


# Development
FROM base AS development

# Set build arguments for UID and GID (default 1000)
ARG UID=1000
ARG GID=1000

# Install su-exec for running commands as different user
RUN apk add --no-cache su-exec libstdc++

# Install Node.js 22.x (official binaries) - overwrites Alpine's older version
# [NOTE] Change Node.js version here if needed (e.g., 18.x or 20.x)
ARG NODE_VERSION=22.12.0
RUN curl -fsSL https://unofficial-builds.nodejs.org/download/release/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64-musl.tar.xz -o /tmp/node.tar.xz \
    && tar -xJf /tmp/node.tar.xz -C /usr/local --strip-components=1 \
    && rm /tmp/node.tar.xz \
    && node --version \
    && npm --version

# Create group and user with host UID/GID
RUN set -x ; \
    addgroup -g "$GID" -S www-data 2>/dev/null || true ; \
    adduser -u "$UID" -D -S -G www-data www-data 2>/dev/null || true

# Copy PHP configuration
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copy application code
COPY --chown=${UID}:${GID} . .

# Install Composer dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Create storage directories
RUN mkdir -p \
    storage/logs \
    storage/app \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache

# Set ownership
RUN chown -R ${UID}:${GID} /var/www

# Create entrypoint script for development
RUN echo '#!/bin/sh' > /entrypoint-dev.sh && \
    echo 'set -e' >> /entrypoint-dev.sh && \
    echo '' >> /entrypoint-dev.sh && \
    echo '# Fix permissions for mounted volumes' >> /entrypoint-dev.sh && \
    echo 'chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true' >> /entrypoint-dev.sh && \
    echo 'chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true' >> /entrypoint-dev.sh && \
    echo '' >> /entrypoint-dev.sh && \
    echo '# Execute command (PHP-FPM will handle user switching)' >> /entrypoint-dev.sh && \
    echo 'exec "$@"' >> /entrypoint-dev.sh && \
    chmod +x /entrypoint-dev.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint-dev.sh"]
CMD ["php-fpm"]

# ==========================================
# Stage 3: Production (Final Image)
# ==========================================
FROM base AS production

# Ensure www-data user exists
RUN set -x ; \
    addgroup -g 82 -S www-data 2>/dev/null || true ; \
    adduser -u 82 -D -S -G www-data www-data 2>/dev/null || true

# Install Node.js 22.x (official binaries) - overwrites Alpine's older version
# [NOTE] Change Node.js version here if needed (e.g., 18.x or 20.x)
ARG NODE_VERSION=22.12.0
RUN curl -fsSL https://unofficial-builds.nodejs.org/download/release/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64-musl.tar.xz -o /tmp/node.tar.xz \
    && tar -xJf /tmp/node.tar.xz -C /usr/local --strip-components=1 \
    && rm /tmp/node.tar.xz \
    && node --version \
    && npm --version

# Copy PHP configuration
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copy composer files for dependency installation
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install Laravel dependencies (production only, no dev packages)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --prefer-dist \
    --no-interaction \
    --no-ansi

# Copy package.json & package-lock.json
COPY --chown=www-data:www-data package*.json ./

# Copy application code (needed for Vite to resolve imports)
COPY --chown=www-data:www-data . .

# Install Node dependencies & Build assets (if package.json exists)
RUN if [ -f package.json ]; then \
        echo "Installing npm dependencies..." && \
        npm install && \
        echo "Generating Wayfinder types/routes (optional)..." && \
        (php artisan wayfinder:generate || echo "Wayfinder skipped - will generate at runtime if needed") && \
        echo "Building frontend assets..." && \
        npm run build && \
        echo "Cleaning up node_modules..." && \
        rm -rf node_modules && \
        echo "Frontend build completed"; \
    else \
        echo "No package.json found, skipping npm build"; \
    fi


# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Create storage & cache directories
RUN mkdir -p \
    storage/logs \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Clear any existing cache
RUN php artisan optimize:clear || true

# Backup public directory for volume initialization
RUN cp -rp /var/www/public /var/www/public-backup

# Create entrypoint script (runs as root, then switches to www-data)
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'set -e' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# ALWAYS sync public assets to host volume' >> /entrypoint.sh && \
    echo 'echo "Syncing public assets..."' >> /entrypoint.sh && \
    echo 'cp -rf /var/www/public-backup/. /var/www/public/' >> /entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/public' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Fix permissions' >> /entrypoint.sh && \
    echo 'chmod -R 775 /var/www/storage 2>/dev/null || true' >> /entrypoint.sh && \
    echo 'chmod -R 775 /var/www/bootstrap/cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo 'chmod -R 755 /var/www/public 2>/dev/null || true' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo '# Execute command as www-data' >> /entrypoint.sh && \
    echo 'exec "$@"' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Install su-exec for running as www-data after init
RUN apk add --no-cache su-exec

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
