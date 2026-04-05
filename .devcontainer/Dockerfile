FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    libffi-dev \
    libonig-dev \
    libedit-dev \
    git

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install ctype curl exif ffi fileinfo ftp gd iconv intl mbstring pdo_mysql phar posix readline sockets sysvsem tokenizer xsl zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY .devcontainer/apache-config.conf /etc/apache2/sites-available/000-default.conf

COPY .devcontainer/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite
WORKDIR /workspace