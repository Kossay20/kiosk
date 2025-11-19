FROM composer:2 AS composer_stage

FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev nodejs npm \
 && docker-php-ext-install pdo pdo_mysql mbstring zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader \
 && npm install \
 && npm run build

ENV PORT=10000

CMD php artisan serve --host=0.0.0.0 --port=${PORT}
