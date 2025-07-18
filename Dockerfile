# --- Stage 1: Build Dependencies ---
FROM composer:2 as vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist --optimize-autoloader


# --- Stage 2: Production Image with Apache ---
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev unzip libpng-dev libjpeg-dev libonig-dev libxml2-dev && \
    docker-php-ext-install pdo pdo_mysql zip mbstring bcmath fileinfo tokenizer xml gd

# Enable mod_rewrite (penting untuk Laravel)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy vendor from previous stage
COPY --from=vendor /app/vendor/ ./vendor/

# Copy entire Laravel app
COPY . .

# Run artisan optimizations
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan storage:link

# Set permission
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Expose port for Railway
EXPOSE 8080

# Override Apache default port to 8080 (Railway needs it)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
