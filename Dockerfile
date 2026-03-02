FROM php:8.3-apache  # Ou php:8.3-cli pour artisan serve optimisé

# Installation des dépendances (inchangé)
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

# Permissions (bon)
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# Apache : active mod_rewrite et document root
RUN a2enmod rewrite \
    && echo '<VirtualHost *:80>\n\
        DocumentRoot /var/www/public\n\
        <Directory /var/www/public>\n\
            AllowOverride All\n\
            Require all granted\n\
        </Directory>\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf

# CMD : Cache + migrate + démarrage Apache (PORT auto sur 80)
CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && apache2-foreground
