FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql gd mbstring exif bcmath opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Install PHP dependencies
COPY composer.json composer.lock* ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application
COPY . .

# Install Node dependencies and build assets
RUN npm install && npm run build

# Laravel setup
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && mkdir -p storage/framework/{views,cache,sessions} storage/logs storage/app/public \
    && chmod -R 775 storage bootstrap/cache

# PHP-FPM config
COPY php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php-fpm"]
