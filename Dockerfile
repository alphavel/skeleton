FROM php:8.2-cli

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
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Swoole extension
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./

# Install composer dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Create required directories with proper permissions
RUN mkdir -p storage/framework \
             storage/logs \
             storage/cache \
             bootstrap/cache \
    && chmod -R 777 storage \
    && chmod -R 777 bootstrap/cache

# Create facades.php file if it doesn't exist
RUN if [ ! -f storage/framework/facades.php ]; then \
        echo '<?php' > storage/framework/facades.php; \
    fi

# Complete the composer installation
RUN composer dump-autoload --optimize

# Expose Swoole port
EXPOSE 9999

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:9999/ || exit 1

# Start application
CMD ["php", "public/index.php"]