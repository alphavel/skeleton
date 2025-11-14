FROM php:8.2-cli

# Install Swoole extension
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 9501

# Start application
CMD ["php", "public/index.php"]