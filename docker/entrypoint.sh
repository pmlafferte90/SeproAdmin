#!/bin/sh
set -e

export PORT="${PORT:-10000}"

# Render el sitio de nginx con el puerto que asigna Render (solo sustituye ${PORT})
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

cd /var/www/html

# Enlace de storage (público) — ignora si ya existe
php artisan storage:link || true

# Cachés de producción — cada una independiente para que un fallo no bloquee el arranque
php artisan config:cache || true
php artisan route:cache  || true
php artisan view:cache   || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/app.conf
