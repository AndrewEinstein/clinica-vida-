#!/usr/bin/env sh
set -eu

# Optional, but helpful in most platforms.
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
  php artisan migrate --force
fi

exec apache2-foreground

