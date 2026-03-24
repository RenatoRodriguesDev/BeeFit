FROM php:8.3-apache

# -------------------------------
# Dependências do sistema
# -------------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && apt-get clean

# -------------------------------
# Extensões PHP
# -------------------------------
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# -------------------------------
# Apache
# -------------------------------
RUN a2enmod rewrite

# 🔥 Reset completo dos MPMs e ativar só prefork
RUN a2dismod mpm_event || true \
 && a2dismod mpm_worker || true \
 && a2dismod mpm_prefork || true \
 && a2enmod mpm_prefork

# 🔹 Definir ServerName para evitar aviso
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# -------------------------------
# Node.js (para Vite)
# -------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# -------------------------------
# Composer
# -------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------------------------------
# Diretório de trabalho
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# Copiar Laravel
# -------------------------------
COPY ./src ./

# Apontar Apache para /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# -------------------------------
# Instalar dependências PHP
# -------------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------------
# Build frontend (Vite)
# -------------------------------
RUN npm ci && npm run build

# -------------------------------
# Criar SQLite + permissões
# -------------------------------
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database

# -------------------------------
# Permissões Laravel
# -------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# -------------------------------
# Storage link
# -------------------------------
RUN php artisan storage:link || true

# -------------------------------
# Cache config
# -------------------------------
RUN php artisan config:clear

# -------------------------------
# Porta do Apache
# -------------------------------
EXPOSE 80

# -------------------------------
# Arranque seguro: Migrations + Seed + Apache
# -------------------------------
CMD ["sh", "-c", "\
apache2ctl configtest && \
php artisan migrate --force && \
php artisan db:seed --force && \
apache2-foreground"]