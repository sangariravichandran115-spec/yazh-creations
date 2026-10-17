#!/bin/sh
set -e

php artisan config:cache
php artisan view:cache
php artisan storage:link --force
php artisan migrate --force

export PORT="${PORT:-10000}"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/conf.d/app.conf

php-fpm -D
exec nginx -g 'daemon off;'
