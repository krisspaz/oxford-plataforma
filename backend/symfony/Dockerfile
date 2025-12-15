FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    acl

RUN docker-php-ext-install \
    pdo_pgsql \
    intl \
    zip \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy project files
COPY . .

# Install PHP dependencies
RUN rm -f composer.lock
RUN APP_ENV=prod composer install --no-dev --optimize-autoloader

# Set permissions
RUN setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
RUN setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

EXPOSE 9000
CMD ["php-fpm"]
