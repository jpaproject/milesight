FROM php:8.3.21-fpm-alpine3.20 AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
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

# Set working directory
WORKDIR /var/www

# Development stage
FROM base AS development

# Create app user
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy PHP configuration for development
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copy application code
COPY --chown=www:www . .

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Create storage directories
RUN mkdir -p storage/logs \
    storage/app \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache

# Change ownership
RUN chown -R www:www /var/www

USER www

EXPOSE 9000

CMD ["php-fpm"]

# Production stage
FROM base AS production

# Create app user
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy PHP configuration for production
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copy application code
COPY --chown=www:www . .

# Install production dependencies only
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# Create storage directories
RUN mkdir -p storage/logs \
    storage/app \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache

# Cache Laravel configuration (with error handling)
RUN php artisan config:cache || echo "Config cache failed - continuing..." && \
    php artisan route:cache || echo "Route cache failed - continuing..." && \
    php artisan view:cache || echo "View cache failed - continuing..."

# Change ownership
RUN chown -R www:www /var/www/storage /var/www/bootstrap/cache

USER www

EXPOSE 9000

CMD ["php-fpm"]
