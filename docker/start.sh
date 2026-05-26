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

# If DB_CONNECTION is not set, Laravel may infer it from DB_URL scheme.
# Force pgsql and, if a DB_URL is present, parse it into standard DB_* vars and unset DB_URL
# to avoid scheme-based driver inference (and typos like "ostgresql://").
if [ -z "${DB_CONNECTION:-}" ]; then
  export DB_CONNECTION="pgsql"
fi

if [ -n "${DB_URL:-}" ]; then
  # Log only the scheme to help debugging without leaking credentials.
  DB_URL_SCHEME="$(printf "%s" "${DB_URL}" | cut -d: -f1)"
  echo "Boot: PORT=${LISTEN_PORT} DB_CONNECTION=${DB_CONNECTION} DB_URL_SCHEME=${DB_URL_SCHEME}"

  # Normalize common scheme typos then export DB_* from the URL.
  case "${DB_URL_SCHEME}" in
    ostgresql)
      DB_URL="postgresql:${DB_URL#ostgresql:}"
      ;;
  esac

  php -r '
    $u = getenv("DB_URL") ?: "";
    $p = parse_url($u);
    if (!$p) { exit(0); }
    $host = $p["host"] ?? "";
    $port = $p["port"] ?? "";
    $db   = isset($p["path"]) ? ltrim($p["path"], "/") : "";
    $user = $p["user"] ?? "";
    $pass = $p["pass"] ?? "";
    if ($host !== "") echo "export DB_HOST=" . escapeshellarg($host) . ";\n";
    if ($port !== "") echo "export DB_PORT=" . escapeshellarg((string)$port) . ";\n";
    if ($db   !== "") echo "export DB_DATABASE=" . escapeshellarg($db) . ";\n";
    if ($user !== "") echo "export DB_USERNAME=" . escapeshellarg($user) . ";\n";
    if ($pass !== "") echo "export DB_PASSWORD=" . escapeshellarg($pass) . ";\n";
  ' > /tmp/db_env.sh || true

  # shellcheck disable=SC1091
  . /tmp/db_env.sh || true
  rm -f /tmp/db_env.sh || true

  unset DB_URL
else
  echo "Boot: PORT=${LISTEN_PORT} DB_CONNECTION=${DB_CONNECTION} DB_URL_SCHEME=<unset>"
fi

# Optional, but helpful in most platforms.
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
  php artisan migrate --force
fi

exec apache2-foreground
