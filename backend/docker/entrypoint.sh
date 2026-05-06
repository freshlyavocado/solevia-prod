#!/bin/sh
set -e

echo "🚀 Starting Solevia Backend..."

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Create storage symlink
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || true

# Cache config, routes, views for performance
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Solevia Backend ready!"

exec "$@"
