FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    acl \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    intl \
    zip \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy project files
COPY . .

# Install PHP dependencies (skip scripts to avoid issues in build)
RUN rm -f composer.lock
RUN APP_ENV=prod COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-scripts

# Run cache:clear manually with warnings allowed
RUN php bin/console cache:clear --env=prod --no-warmup || true
RUN php bin/console cache:warmup --env=prod || true

# Set permissions
RUN setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var || true
RUN setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var || true

EXPOSE 9000
CMD ["php-fpm"]
