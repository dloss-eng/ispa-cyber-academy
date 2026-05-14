FROM php:8.3-cli

# Installer Node.js 20 (LTS) proprement
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Extensions PHP + dépendances
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev \
    libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd \
    && apt-get clean

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier d'abord les fichiers de dépendances
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction

COPY package.json package-lock.json ./
RUN npm ci

# Copier le reste du projet
COPY . .

# Build des assets Vite
RUN npm run build

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8000
