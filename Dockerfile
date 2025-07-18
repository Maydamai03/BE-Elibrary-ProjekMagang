# --- Stage 1: Build Dependencies ---
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
# Install dependencies but skip running scripts (like artisan) because the full app is not here yet.
RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist --optimize-autoloader


# --- Stage 2: Final Production Image ---
FROM php:8.1-fpm-alpine

# Install system dependencies for PHP extensions
# gd extension needs libpng, libjpeg, freetype
RUN apk add --no-cache \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev # for mbstring

# Install PHP extensions required by Laravel
# Added: bcmath, fileinfo, gd, mbstring, tokenizer, xml, zip
RUN docker-php-ext-install \
    pdo pdo_mysql \
    bcmath \
    fileinfo \
    gd \
    mbstring \
    tokenizer \
    xml \
    zip

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
