FROM composer:2 AS composer_stage

FROM php:8.2-cli

# Ajout de sqlite3 + extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev nodejs npm \
    sqlite3 libsqlite3-dev \
 && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

# Installer dépendances et builder
RUN composer install --no-dev --optimize-autoloader \
 && npm install \
 && npm run build \
 # Créer la base sqlite et lancer les migrations + seed
 && mkdir -p database \
 && touch database/database.sqlite \
 && php artisan migrate --force --seed

ENV PORT=10000

CMD php artisan serve --host=0.0.0.0 --port=${PORT}
