#!/bin/sh
set -eu

cd /var/www/html

mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch database/database.sqlite
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

php artisan optimize:clear
php artisan config:cache
php artisan route:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

exec apache2-foreground
