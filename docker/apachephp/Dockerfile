# ─────────────────────────────────────────────────────
# Imagen: Apache + PHP 8.2 + extensiones + XDebug
# ─────────────────────────────────────────────────────
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libzip-dev \
        zip \
        unzip \
        curl \
    && docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# XDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# mod_rewrite necesario para el Router MVC
RUN a2enmod rewrite && a2enmod headers

COPY apache2.conf /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/cache \
    && chown www-data:www-data /var/www/html/cache \
    && chmod 775 /var/www/html/cache

EXPOSE 80
