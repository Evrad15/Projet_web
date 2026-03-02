FROM php:8.3-apache
RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev zip unzip curl \
    && docker-php-ext-install intl zip pdo pdo_mysql bcmath \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN a2enmod rewrite
RUN sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE ${PORT:-8080}
CMD php artisan config:cache \
    && php artisan migrate --force \
    && apache2-foreground
