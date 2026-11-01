FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql mbstring \
    && rm -rf /var/lib/apt/lists/*

RUN printf '%s\n' \
    'short_open_tag=Off' \
    'expose_php=Off' \
    'display_errors=Off' \
    'log_errors=On' \
    > /usr/local/etc/php/conf.d/zz-app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

CMD ["sh", "-c", "php artisan config:cache && php artisan view:cache && php artisan storage:link --force && php artisan migrate --force && exec env PHP_CLI_SERVER_WORKERS=4 php artisan serve --host=0.0.0.0 --port=${PORT:-10000} --no-reload"]
