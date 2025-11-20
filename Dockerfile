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
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Install Swoole extension
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Configure Opcache & JIT (Otimizado para Performance)
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user and group with same UID/GID as host (default 1000)
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} alphavel || true \
    && useradd -u ${USER_ID} -g alphavel -m -s /bin/bash alphavel || true

# Set working directory
WORKDIR /var/www

# Copy application files
COPY --chown=alphavel:alphavel . .

# Create required directories with proper permissions BEFORE composer
RUN mkdir -p storage/framework \
             storage/logs \
             storage/cache \
             bootstrap/cache \
    && chown -R alphavel:alphavel storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Switch to alphavel user
USER alphavel

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Expose Swoole port
EXPOSE 9999

# Health check - use /json endpoint that actually exists
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:9999/json || exit 1

# Start application
CMD ["php", "public/index.php"]