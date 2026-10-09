#!/bin/sh
# SRP: start the PHP runtime and the HTTP front, then stay in the foreground.
set -eu

cd /var/www/html

# Present so phpdotenv does not warn. Values come from the Compose environment.
touch .env

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --no-scripts
fi

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ ! -f bootstrap/cache/packages.php ]; then
    php artisan package:discover --ansi
fi

chown -R www-data:www-data storage bootstrap/cache

php-fpm -D
exec nginx -g 'daemon off;'
