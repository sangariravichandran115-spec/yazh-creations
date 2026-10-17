FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    gettext-base \
    libpq-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql mbstring \
    && rm -rf /var/lib/apt/lists/* \
    && printf 'listen = 127.0.0.1:9000\n' > /usr/local/etc/php-fpm.d/zz-listen.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache \
    && rm -f /etc/nginx/sites-enabled/default

COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
