FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

RUN a2enmod rewrite

# 🔥 FIX MPM
RUN a2dismod mpm_event || true \
 && a2dismod mpm_worker || true \
 && a2enmod mpm_prefork

# Node
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 🔥 copia código (IMPORTANTE para Railway)
COPY ./src .

# 🔥 Laravel setup
RUN mkdir -p storage bootstrap/cache \
 && composer install --no-dev --optimize-autoloader \
 && npm ci && npm run build || true \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# Apache → public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["sh", "-c", "\
mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs storage/app/public && \
chmod -R 775 storage bootstrap/cache && \
chown -R www-data:www-data storage bootstrap/cache && \
php artisan storage:link --force 2>/dev/null || true && \
php artisan migrate --force && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
rm -f /etc/apache2/mods-enabled/mpm_* && \
a2enmod mpm_prefork && \
apache2ctl configtest && \
apache2-foreground"]