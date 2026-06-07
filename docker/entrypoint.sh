#!/usr/bin/env bash
set -e

# Render (y otros PaaS) inyectan el puerto a usar en la variable $PORT.
# Apache escucha en 80 por defecto: lo redirigimos al $PORT recibido.
PORT="${PORT:-80}"
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Enlace simbólico para servir los ficheros subidos (avatares) desde /public.
php artisan storage:link || true

# Cachear configuración y rutas para producción (más rápido).
php artisan config:cache || true
php artisan route:cache || true

# Aplicar migraciones y sembrar los roles base (admin, etc.) sin perder datos.
php artisan migrate --force || true
php artisan db:seed --class=RoleSeeder --force || true

# Asignar rol admin a los emails de la variable ADMIN_EMAILS (idempotente).
php artisan db:seed --class=AdminSeeder --force || true

# Reasegurar permisos de storage ANTES de arrancar Apache: los comandos artisan
# de arriba se ejecutan como root y pueden crear ficheros (p. ej. laravel.log)
# que luego www-data no podría escribir, provocando errores en cascada.
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Arrancar Apache en primer plano.
exec apache2-foreground
