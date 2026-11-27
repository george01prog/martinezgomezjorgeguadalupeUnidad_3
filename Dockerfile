# ---------- Etapa 1: compilar CSS/JS con Vite ----------
FROM node:20-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Etapa 2: aplicación Laravel (PHP-FPM + Nginx) ----------
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx git unzip curl \
        libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=assets /build/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s \
    CMD curl -fs http://127.0.0.1/up || exit 1
CMD ["/entrypoint.sh"]
