FROM php:8.3-apache

# Dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev

# Extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Apache
RUN a2enmod rewrite

# ✅ FIX MPM
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# Node (para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar Laravel (está em /src)
COPY ./src ./

# Apontar Apache para /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Build frontend (Vite)
RUN npm ci && npm run build && ls -la public/build

# 🔥 CRIAR SQLITE + PERMISSÕES (FIX PRINCIPAL)
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database

# Permissões Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Storage link
RUN php artisan storage:link || true

# Cache config (evita bugs)
RUN php artisan config:clear

EXPOSE 80

# 🚀 Arranque (migrate automático)
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && apache2-foreground"]