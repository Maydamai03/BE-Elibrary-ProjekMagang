# # Gunakan image PHP-FPM resmi
# FROM php:8.1-fpm

# # Set working directory
# WORKDIR /var/www

# # Install dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev \
#     libonig-dev \
#     libxml2-dev \
#     libzip-dev \
#     zip \
#     unzip \
#     curl \
#     git \
#     && docker-php-ext-configure gd --with-jpeg \
#     && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copy existing application
# COPY . /var/www

# # Set folder permission
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# # Expose port
# EXPOSE 9000

# CMD ["php-fpm"]

# --- Stage 1: Install PHP & System Dependencies ---
FROM php:8.1-fpm-alpine as base
# Set working directory
WORKDIR /var/www
# Install system dependencies & build tools
RUN apk add --no-cache \
        $PHPIZE_DEPS \
        libpng-dev \
        jpeg-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev
# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql bcmath fileinfo mbstring tokenizer xml zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# --- Stage 2: Install Composer Dependencies ---
FROM base as vendor
# Copy composer files
COPY database/ database/
COPY composer.json composer.lock ./
# Install composer dependencies
RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist --optimize-autoloader

# --- Stage 3: Final Production Image ---
FROM base as production
# Copy application files and vendor directory
COPY . .
COPY --from=vendor /var/www/vendor/ ./vendor/

# Optimize application for production
RUN php artisan config:cache
RUN php artisan route:cache
# Run storage:link during build
RUN php artisan storage:link

# Set correct file ownership for web server
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000
# Command to run PHP-FPM
CMD ["php-fpm"]
