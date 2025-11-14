# Multi-stage para otimização, revertido para Debian para melhor performance Swoole
FROM php:8.3-cli AS builder

RUN apt-get update && apt-get install -y git unzip libbrotli-dev autoconf build-essential linux-headers-generic
RUN docker-php-ext-install pdo pdo_mysql opcache
RUN pecl install swoole && docker-php-ext-enable swoole opcache

COPY composer.json /app/
WORKDIR /app
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    libbrotli-dev autoconf build-essential linux-headers-generic \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_mysql opcache pcntl sockets
RUN pecl install swoole && docker-php-ext-enable swoole opcache

# Copie apenas arquivos necessários
COPY --from=builder /app/vendor /app/vendor
COPY . /app
WORKDIR /app

# php.ini otimizado com preload para performance
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.preload=/app/preload.php" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini

# Script de preload
RUN echo "<?php opcache_compile_file('/app/app/Core/Application.php'); opcache_compile_file('/app/app/Core/Router.php'); ?>" > /app/preload.php

EXPOSE 9501
CMD ["php", "public/index.php"]