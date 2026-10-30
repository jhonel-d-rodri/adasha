#!/bin/sh
set -eu

cd /var/www/html
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p "$(dirname "${DB_DATABASE:-/var/www/html/database/database.sqlite}")"
    touch "${DB_DATABASE:-/var/www/html/database/database.sqlite}"
fi

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
