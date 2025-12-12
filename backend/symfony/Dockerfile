FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    acl \
    && docker-php-ext-install \
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
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
RUN setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

EXPOSE 9000
CMD ["php-fpm"]
