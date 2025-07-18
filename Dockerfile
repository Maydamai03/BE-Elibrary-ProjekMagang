# --- Stage 1: Build Dependencies ---
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
# Install dependencies but skip running scripts (like artisan) because the full app is not here yet.
RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist --optimize-autoloader


# --- Stage 2: Final Production Image ---
FROM php:8.1-fpm-alpine

# Install system dependencies & build tools
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev

# Install PHP extensions one by one or in small groups to manage memory
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install tokenizer
RUN docker-php-ext-install xml
RUN docker-php-ext-install zip
# GD is often memory intensive, so it gets its own layer
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Clean up build dependencies to keep image size small
RUN apk del $PHPIZE_DEPS

# Set working directory
WORKDIR /app

# Copy files from 'vendor' stage and application files
COPY --from=vendor /app/vendor/ ./vendor/
COPY . .

# Optimize application for production
RUN php artisan config:cache
RUN php artisan route:cache

# Run storage:link
RUN php artisan storage:link

# Set correct file ownership for web server
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Command to run PHP-FPM
CMD ["php-fpm"]
