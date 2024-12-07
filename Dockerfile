FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
