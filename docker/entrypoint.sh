#!/bin/sh
set -e
cd /var/www/html

echo "Esperando a la base de datos en $DB_HOST:${DB_PORT:-3306}..."
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306).';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
  sleep 3
done
echo "Base de datos lista."

php artisan config:cache
php artisan route:cache
php artisan view:cache

# En varias réplicas, solo una debe migrar (RUN_MIGRATIONS=false en las demás)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi

# Datos de ejemplo (usuarios, categorías, productos). Solo la primera vez.
if [ "${RUN_SEED:-false}" = "true" ]; then
  php artisan db:seed --force || true
fi

php-fpm -D
exec nginx -g "daemon off;"
