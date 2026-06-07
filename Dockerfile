# ── UniCar backend (Laravel 12 / PHP 8.4) — imagen para Render ──────────────
# PHP 8.4 porque composer.lock resuelve symfony 8.0 (requiere >=8.4) y
# spatie/laravel-permission (requiere >=8.3). Coincide con el PHP local.

# ── Etapa 1: compilar los assets del panel de admin (Vue 3 + Inertia) ───────
# Se hace en una imagen Node aparte para no instalar Node en la imagen final.
# El resultado (public/build con el manifest de Vite) se copia a la etapa PHP.
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
RUN npm run build

# ── Etapa 2: imagen PHP de producción ───────────────────────────────────────
FROM php:8.4-apache

# Dependencias del sistema necesarias para las extensiones de PHP
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        gd \
        zip \
        exif \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# Configuración de OPcache (acelera la ejecución de PHP)
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Activar mod_rewrite (necesario para las rutas de Laravel)
RUN a2enmod rewrite

# Composer (copiado desde la imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instalar dependencias primero (mejor cacheo de capas Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copiar el resto de la aplicación
COPY . .

# Copiar los assets ya compilados desde la etapa de Node (Vue + Inertia + Vite)
COPY --from=assets /app/public/build ./public/build

# Generar el autoloader optimizado y descubrir paquetes
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi || true

# Permisos para storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configuración de Apache: docroot a /public
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Entrypoint: ajusta el puerto, cachea config, migra y arranca Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
