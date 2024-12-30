# Dockerfile
FROM php:8.2-apache

# Install necessary packages
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    git

# Install necessary PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip

# Activate Apache modules headers & rewrite
RUN a2enmod headers rewrite

# Copy virtual host configuration from current path onto existing 000-default.conf
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Set the working directory and copy the composer.json and composer.lock files
WORKDIR /var/www/html
COPY composer.json composer.lock ./
    
# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 for web traffic
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]