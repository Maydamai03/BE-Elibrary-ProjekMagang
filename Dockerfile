# --- Stage 1: Build Dependencies ---
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader


# --- Stage 2: Final Production Image ---
FROM php:8.1-fpm-alpine

# Install ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /app

# Copy file dari stage 'vendor' dan file aplikasi
COPY --from=vendor /app/vendor/ ./vendor/
COPY . .

# Jalankan storage:link
RUN php artisan storage:link

# Atur kepemilikan file agar web server bisa menulis ke storage & bootstrap/cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Perintah untuk menjalankan PHP-FPM
CMD ["php-fpm"]
