#!/bin/sh
set -e

# S'assurer que le fichier SQLite existe
mkdir -p database
touch database/database.sqlite

# Lancer les migrations + seed (si déjà faits, ça échouera pas grave)
php artisan migrate --force --seed || echo "Migrations déjà faites ou erreur bénigne"

# Lancer le serveur Laravel
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
