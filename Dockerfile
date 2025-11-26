FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libbrotli-dev \
    autoconf \
    g++ \
    make \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Install Swoole extension
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Install APCu
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# Configure PHP
RUN { \
    echo 'memory_limit=512M'; \
    echo 'max_execution_time=300'; \
    echo 'max_input_time=300'; \
    echo 'post_max_size=32M'; \
    echo 'upload_max_filesize=32M'; \
    echo 'expose_php=Off'; \
    echo 'date.timezone=UTC'; \
    } > /usr/local/etc/php/conf.d/performance.ini

# Configure Opcache + JIT (Production Optimized)
# Critical for Swoole CLI mode: opcache.enable_cli=1 must be enabled
# JIT (Just-In-Time compiler) provides 10-30% performance boost for PHP 8.x
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.fast_shutdown=1'; \
    echo ''; \
    echo '; JIT Configuration (PHP 8.0+)'; \
    echo 'opcache.jit_buffer_size=100M'; \
    echo 'opcache.jit=tracing'; \
    } > /usr/local/etc/php/conf.d/opcache-custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first
COPY composer.json ./

# Install dependencies as root (will create fresh composer.lock)
# Composer will fetch alphavel/alphavel:dev-main from GitHub
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-interaction \
    --no-progress \
    --ignore-platform-req=ext-swoole \
    && composer clear-cache

# Copy rest of application files
COPY . .

# Create directories and set permissions
RUN mkdir -p storage/framework storage/logs storage/cache bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && chmod 666 composer.lock 2>/dev/null || true

# Copy and configure entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose Swoole port
EXPOSE 9999

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -sf http://localhost:9999/json || exit 1

# Use entrypoint to ensure permissions
ENTRYPOINT ["docker-entrypoint.sh"]

# Start application
CMD ["php", "public/index.php"]