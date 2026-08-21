# =====================================================
# Dockerfile untuk Pertashop di Render
# PHP 8.2 + Apache + PostgreSQL support
# =====================================================

FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (untuk .htaccess)
RUN a2enmod rewrite

# Set document root ke /var/www/html
WORKDIR /var/www/html

# Copy semua file project
COPY . /var/www/html/

# Set permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Config Apache agar AllowOverride All (biar .htaccess bisa jalan)
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/pertashop.conf \
    && a2enconf pertashop

# Port yang dipakai Apache
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
