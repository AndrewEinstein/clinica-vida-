#!/usr/bin/env sh
set -eu

# Render and similar platforms provide the listening port via $PORT.
# Apache defaults to 80, so we rewrite its config at runtime.
LISTEN_PORT="${PORT:-80}"
if [ "$LISTEN_PORT" != "80" ]; then
  sed -i "s/^[[:space:]]*Listen[[:space:]]\\+80[[:space:]]*$/Listen ${LISTEN_PORT}/" /etc/apache2/ports.conf || true
  sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:${LISTEN_PORT}>/" /etc/apache2/sites-available/000-default.conf || true
fi

# Defensive: some hosts/UI copy-paste can end up with DB_CONNECTION=ostgresql/postgresql.
# Laravel expects "pgsql". If we see common variants, normalize before Artisan runs.
case "${DB_CONNECTION:-}" in
  postgresql|ostgresql)
    export DB_CONNECTION="pgsql"
    ;;
esac

echo "Boot: PORT=${LISTEN_PORT} DB_CONNECTION=${DB_CONNECTION:-<unset>}"

# Optional, but helpful in most platforms.
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
  php artisan migrate --force
fi

exec apache2-foreground
