# Étape 1 : image avec Composer
FROM composer:2 AS composer_stage

# Étape 2 : image principale PHP
FROM php:8.2-cli

# Install des dépendances système (git, node, npm, etc.)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev nodejs npm \
 && docker-php-ext-install pdo pdo_mysql mbstring zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Dossier de travail
WORKDIR /app

# Copier tout le projet
COPY . .

# Copier composer depuis l'étape 1
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

# Installer dépendances PHP + JS et builder Vite
RUN composer install --no-dev --optimize-autoloader \
 && npm install \
 && npm run build

# Port utilisé par Render
ENV PORT=10000

# Lancer Laravel
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
