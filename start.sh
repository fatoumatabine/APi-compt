#!/bin/bash

# Exécuter les migrations si la base de données est disponible
php artisan migrate --force || echo "Migration failed, continuing..."

# Démarrer Apache
apache2-foreground
