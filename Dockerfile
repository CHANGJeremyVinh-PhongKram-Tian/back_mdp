# 1. Utiliser une image PHP officielle avec FPM
FROM php:8.4-fpm

# 2. Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# 3. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Définir le répertoire de travail
WORKDIR /var/www

# 5. Copier les fichiers du projet
COPY . .

# 6. Installer les dépendances PHP (sans les outils de dev)
RUN composer install --no-dev --optimize-autoloader

# 7. Donner les droits sur les dossiers storage et bootstrap (crucial pour Laravel)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 8. Exposer le port et lancer le serveur
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000