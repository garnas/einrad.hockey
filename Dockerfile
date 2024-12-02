# Dockerfile
FROM php:8.2-apache

# Install necessary PHP extensions including GD and ZIP, and Composer
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip \
    && a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory and copy the composer.json and composer.lock files
WORKDIR /var/www/html
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code except the vendor directory
COPY . .

# Set the DocumentRoot to public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Allow Doctrine access to tmp
RUN chmod 777 tmp

# Expose port 80 for web traffic
EXPOSE 80
