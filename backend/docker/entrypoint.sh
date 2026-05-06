#!/bin/sh
set -e

echo "=== Starting Solevia Backend ==="

# Create .env file from environment variables if it doesn't exist
if [ ! -f .env ]; then
    echo "[0/4] Creating .env from environment..."
    env | grep -E '^(APP_|DB_|CACHE_|SESSION_|QUEUE_|SANCTUM_|FRONTEND_|MAIL_|LOG_)' > .env 2>/dev/null || true
fi

# Generate app key if not set
if [ -z "$APP_KEY" ] && ! grep -q "APP_KEY=base64" .env 2>/dev/null; then
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

# Cache routes and views (NOT config — Docker env vars must be read at runtime)
echo "[4/4] Caching routes and views..."
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

echo "=== Solevia Backend ready! ==="

exec "$@"
