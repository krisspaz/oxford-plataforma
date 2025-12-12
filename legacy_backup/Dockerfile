# ---- Composer stage ----
FROM composer:lts AS vendor

WORKDIR /composer
COPY . .

RUN composer install \
        --ignore-platform-reqs \
        --no-ansi \
        # --no-dev \
        --no-interaction \
        --no-progress \
        # --no-scripts \
        --prefer-dist \
        --optimize-autoloader

# ---- Node stage ----
FROM node:lts-alpine AS node

ENV CI=true

WORKDIR /node
COPY . .
RUN corepack enable pnpm  \
    && pnpm i --production \
    && pnpm build

# ---- Debian stage ----
FROM debian:stable-slim AS prod

ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /composer/vendor vendor
COPY --from=vendor /composer/public/vendor /var/www/html/public/vendor
COPY --from=node /node/public/build /var/www/html/public/build

RUN apt-get update && apt-get -y upgrade \
    && apt-get install -y --no-install-recommends \
    # 1. install base packages
    zip unzip ca-certificates xz-utils curl supervisor nginx apt-transport-https lsb-release \
    # 2. add deb.sury.org repository for php
    && curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb \
    && dpkg -i /tmp/debsuryorg-archive-keyring.deb \
    && sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' \
    # 3. install php and extensions
    && apt-get update && apt-get install -y --no-install-recommends \
    php7.4 php7.4-fpm \
    php7.4-mysql php7.4-sqlite3 \
    php7.4-xml \
    php7.4-bcmath php7.4-curl \
    php7.4-gd php7.4-imagick \
    php7.4-intl \
    php7.4-mbstring \
    php7.4-soap \
    php7.4-tokenizer \
    php7.4-zip \
    php7.4-cli \
    # 4. clean up
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    # 5. adjust permissions
    && chown -R nobody:nogroup /var/www/html /run /var/lib/nginx /var/log/nginx

# configure nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/ /etc/nginx/conf.d/

# configure fpm and php
COPY docker/php/fpm-pool.conf /etc/php/7.4/fpm/pool.d/www.conf
COPY docker/php/php.ini  /etc/php/7.4/cli/conf.d/99-corpo-rest.ini

# configure supervisord
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

USER nobody

EXPOSE 8000

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
