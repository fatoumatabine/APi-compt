# Étape 1: Build des dépendances PHP
FROM composer:2.6 AS composer-build

WORKDIR /app

# Copier les fichiers de dépendances
COPY composer.json composer.lock ./

# Installer les dépendances PHP sans scripts post-install
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Étape 2: Image finale pour l'application
FROM php:8.3-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
libpq-dev \
postgresql-client \
&& docker-php-ext-install pdo pdo_pgsql \
&& a2enmod rewrite \
&& sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -i 's/*:80/*:${PORT}/' /etc/apache2/sites-available/000-default.conf

# Créer un utilisateur non-root
RUN groupadd -g 1000 laravel && useradd -u 1000 -g laravel -s /bin/bash -m laravel

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les dépendances installées depuis l'étape de build
COPY --from=composer-build /app/vendor ./vendor

# Copier le reste du code de l'application
COPY . .

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p storage/framework/{cache,data,sessions,testing,views} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R laravel:laravel /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Créer un fichier .env minimal pour le build
RUN echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=http://localhost" >> .env && \
    echo "" >> .env && \
    echo "LOG_CHANNEL=stack" >> .env && \
    echo "LOG_LEVEL=error" >> .env && \
    echo "" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=\${DB_HOST}" >> .env && \
    echo "DB_PORT=\${DB_PORT}" >> .env && \
    echo "DB_DATABASE=\${DB_DATABASE}" >> .env && \
    echo "DB_USERNAME=\${DB_USERNAME}" >> .env && \
    echo "DB_PASSWORD=\${DB_PASSWORD}" >> .env && \
    echo "" >> .env && \
    echo "CACHE_DRIVER=file" >> .env && \
    echo "SESSION_DRIVER=file" >> .env && \
    echo "QUEUE_CONNECTION=sync" >> .env

# Changer les permissions du fichier .env pour l'utilisateur laravel
RUN chown laravel:laravel .env

# Générer la clé d'application et optimiser
# Générer la clé d'application et optimiser
USER laravel
RUN php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan l5-swagger:generate && \
    chmod -R 775 storage/api-docs && \
    ln -s /var/www/html/storage/api-docs /var/www/html/public/docs
USER root

# Copier le script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copier la configuration Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Exposer le port 80
EXPOSE 80

# Point d'entrée et commande par défaut
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
