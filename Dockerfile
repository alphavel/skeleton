FROM php:8.4-cli

# Install system dependencies (oniguruma MUST be installed before mbstring)
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

# Install PHP extensions (mbstring requires libonig-dev)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Install Swoole extension with optimized flags
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Configure PHP for maximum performance
RUN { \
    echo 'memory_limit=512M'; \
    echo 'max_execution_time=300'; \
    echo 'max_input_time=300'; \
    echo 'post_max_size=32M'; \
    echo 'upload_max_filesize=32M'; \
    echo 'expose_php=Off'; \
    echo 'date.timezone=UTC'; \
    } > /usr/local/etc/php/conf.d/performance.ini

# Configure Opcache & JIT (Ultra-Optimized for PHP 8.4)
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.jit=tracing'; \
    echo 'opcache.jit_buffer_size=256M'; \
    echo 'opcache.memory_consumption=512'; \
    echo 'opcache.interned_strings_buffer=32'; \
    echo 'opcache.max_accelerated_files=100000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.huge_code_pages=1'; \
    echo 'opcache.file_cache=/tmp/opcache'; \
    echo 'opcache.file_cache_only=0'; \
    echo 'opcache.optimization_level=0x7FFEBFFF'; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Create opcache file cache directory
RUN mkdir -p /tmp/opcache && chmod 777 /tmp/opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user and group with same UID/GID as host (default 1000)
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} alphavel || true \
    && useradd -u ${USER_ID} -g alphavel -m -s /bin/bash alphavel || true

# Set working directory and create base structure as root
WORKDIR /var/www

# Create ALL required directories with proper permissions FIRST (as root)
RUN mkdir -p storage/framework \
             storage/logs \
             storage/cache \
             bootstrap/cache \
             vendor \
    && chown -R alphavel:alphavel . \
    && chmod -R 775 storage bootstrap/cache vendor

# Copy application files (already owned by alphavel)
COPY --chown=alphavel:alphavel . .

# Switch to alphavel user
USER alphavel

# Install composer dependencies with aggressive optimization
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --ignore-platform-req=ext-swoole

# Warm up opcache by preloading all PHP files (as user alphavel)
RUN find /var/www -type f -name "*.php" -exec php -d opcache.enable=1 -d opcache.enable_cli=1 {} \; 2>/dev/null || true

# Pre-compile autoloader for faster loading
RUN composer dump-autoload --classmap-authoritative --no-dev

# Expose Swoole port
EXPOSE 9999

# Health check with reduced interval for faster detection
HEALTHCHECK --interval=15s --timeout=2s --start-period=30s --retries=3 \
    CMD curl -sf http://localhost:9999/json || exit 1

# Start application with optimized PHP settings
CMD ["php", "-d", "opcache.enable=1", "-d", "opcache.enable_cli=1", "public/index.php"]