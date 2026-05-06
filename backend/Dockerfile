# ============================================
# Solevia Backend — Laravel 13 + PHP 8.3
# ============================================

# --- Stage 1: Composer dependencies ---
FROM composer:2 AS composer-deps

# Install intl extension (required by Filament)
RUN apk add --no-cache icu-dev && docker-php-ext-install intl

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# --- Stage 2: Production image ---
FROM php:8.3-fpm-alpine

# Install system deps & PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        icu-libs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && rm -rf /var/cache/apk/*

# PHP production config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini /usr/local/etc/php/conf.d/99-solevia.ini

# Nginx config — remove default, add ours
RUN rm -f /etc/nginx/http.d/default.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Supervisor config
COPY docker/supervisord.conf /etc/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy composer binary from stage 1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy vendor from composer stage
COPY --from=composer-deps /app/vendor ./vendor

# Copy application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Create required directories & set permissions
RUN mkdir -p storage/logs \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        bootstrap/cache \
        public/storage \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
