# Base image: PHP 8.3 with Apache on Debian
FROM php:8.3-apache

# Debian: Install system dependencies
RUN apt-get update && apt-get install -y git unzip

# PHP: Install the extension installer script
# See: https://hub.docker.com/_/php#php-core-extensions and https://github.com/mlocati/docker-php-extension-installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# PHP: Install required extensions
RUN install-php-extensions \
    calendar ctype curl exif ffi fileinfo ftp \
    gd iconv intl mbstring openssl pdo_mysql phar \
    readline posix sockets sysvsem tokenizer \
    xsl zip xdebug

# PHP: Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP: Configure Xdebug
COPY .devcontainer/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Apache: enable rewrite module and set virtual host
RUN a2enmod rewrite
COPY .devcontainer/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Dev Container/Docker: Set working directory
WORKDIR /workspace
