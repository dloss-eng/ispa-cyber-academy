FROM php:8.3-cli

# Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Extensions PHP
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev \
    libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd \
    && apt-get clean

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier tout le projet
COPY . .

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Installer et builder les assets
RUN npm ci && npm run build

# Permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Seeder conditionnel : ne seede que si la table roles est vide
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan tinker --execute="if(\App\Models\Role::count() === 0) { \Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]); echo 'Seeded!'; } else { echo 'Already seeded, skipping.'; }" && \
    php artisan serve --host=0.0.0.0 --port=8000
