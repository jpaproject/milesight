FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev unzip \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/webapp

# Copy project files
COPY . .

# Set permission untuk Laravel
RUN chown -R www-data:www-data /var/www/webapp \
    && chmod -R 775 /var/www/webapp/storage /var/www/webapp/bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
