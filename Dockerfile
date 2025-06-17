FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    zip unzip curl libpq-dev git \ 
    && docker-php-ext-install pdo_pgsql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]