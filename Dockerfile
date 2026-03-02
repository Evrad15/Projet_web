FROM php:8.3-apache

# Installation des dépendances
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install intl zip pdo pdo_mysql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# Apache : mod_rewrite + document root
RUN a2enmod rewrite \
    && echo '<VirtualHost *:${PORT:-8080}>\n\
        DocumentRoot /var/www/public\n\
        <Directory /var/www/public>\n\
            AllowOverride All\n\
            Require all granted\n\
        </Directory>\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf

# Expose le port Railway
EXPOSE ${PORT:-8080}

# CMD avec PORT variable Railway
CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && exec apache2-foreground
