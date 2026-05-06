#!/bin/sh
set -e

echo "=== Starting Solevia Backend ==="

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "[1/4] Generating application key..."
    php artisan key:generate --force
fi

# Wait a moment for DB to be fully ready
echo "[2/4] Running migrations..."
php artisan migrate --force || {
    echo "Migration failed, retrying in 5s..."
    sleep 5
    php artisan migrate --force
}

# Create storage symlink
echo "[3/4] Creating storage link..."
php artisan storage:link 2>/dev/null || true

# DO NOT cache config — Docker env vars must be read at runtime
echo "[4/4] Caching routes and views..."
php artisan route:cache || true
php artisan view:cache || true

echo "=== Solevia Backend ready! ==="

exec "$@"
