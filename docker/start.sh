#!/bin/sh
set -eu

echo "[start] container booting"
cd /var/www/html
echo "[start] cwd=$(pwd)"

mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch database/database.sqlite
echo "[start] sqlite path=$(pwd)/database/database.sqlite"
ls -la database || true
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
echo "[start] storage permissions prepared"

echo "[start] clearing optimize cache"
php artisan optimize:clear
echo "[start] caching config"
php artisan config:cache
echo "[start] caching routes"
php artisan route:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "[start] running migrations"
    php artisan migrate --force
fi

echo "[start] launching apache"
exec apache2-foreground
