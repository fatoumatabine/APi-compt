FROM php:8.2-apache

# Installer les dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

# Configurer Apache pour utiliser le port dynamique de Render
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer.json et composer.lock pour installer les dépendances
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Copier le reste du code
COPY . .

# Lier le stockage
RUN php artisan storage:link

# Copier la configuration Apache personnalisée et le script de démarrage
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Activer la nouvelle configuration du site et désactiver l'ancienne
RUN a2dissite 000-default.conf && a2ensite 000-default.conf

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["/usr/local/bin/start.sh"]
