#!/bin/bash
set -e

cd /var/www/html

# APP_KEY / DB_* etc. are expected to be provided as real environment
# variables (Render dashboard, docker-compose env_file...). No .env file
# is required or copied into the image.

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# S'assure que le stockage (potentiellement monté comme volume) est
# accessible en écriture par PHP-FPM, puis crée le lien public.
mkdir -p storage/app/public
chown -R www-data:www-data storage
php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
