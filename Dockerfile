FROM php:8.3-fpm

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

# Création des dossiers nécessaires ET permissions en une seule fois
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# On ne fait PAS les artisan cache ici pour éviter les erreurs de connexion DB au build

# Commande de démarrage : Migrations + Seed + Cache + Serve
CMD php artisan migrate --force --seed && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}