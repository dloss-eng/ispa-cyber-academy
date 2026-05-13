FROM php:8.3-cli
RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libonig-dev libxml2-dev nodejs npm && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd && apt-get clean
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --optimize-autoloader --no-dev --no-interaction
RUN npm install && npm run build
RUN chmod -R 775 storage bootstrap/cache
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
