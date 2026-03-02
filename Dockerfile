FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install intl zip pdo pdo_mysql bcmath \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8080

RUN mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/framework/cache \
    && chown -R www-data:www-data /var/www/storage
    
# Pas besoin d'EXPOSE fixe, Railway gère le mapping
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}