# syntax=docker/dockerfile:1

############################
# Stage 1 — Compilar assets con Vite (Node)
############################
FROM node:20-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

############################
# Stage 2 — Dependencias PHP con Composer
# (sobre php:8.2 para que coincida con el runtime; la imagen composer:2
#  trae PHP flotante y rompe el lock cuando sube de versión)
############################
FROM php:8.2-cli-bookworm AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y --no-install-recommends git unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
WORKDIR /app
COPY composer.json composer.lock ./
# gd/exif/zip no existen en esta etapa (solo instala paquetes, no ejecuta la app);
# el runtime sí las compila
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction \
    --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif --ignore-platform-req=ext-zip
COPY . .
RUN composer dump-autoload --optimize --no-dev

############################
# Stage 3 — Runtime: PHP-FPM + nginx
############################
FROM php:8.2-fpm-bookworm AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor gettext-base \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev zip unzip libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip gd bcmath exif pcntl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Código + vendor (stage 2) y assets compilados (stage 1)
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

# Configuración
COPY docker/php.prod.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
