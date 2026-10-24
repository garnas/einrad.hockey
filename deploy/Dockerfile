FROM php:8.3-apache

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
# Nur die nicht im Basis-Image enthaltenen Extensions aus composer.json
RUN install-php-extensions calendar exif ftp gd intl pdo_mysql xsl zip \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY . .
COPY deploy/env.php ./env.php
RUN composer dump-autoload --optimize --no-dev \
    && php bin/doctrine orm:generate-proxies || true
RUN mkdir -p cache system/logs system/backups public/uploads/s \
    && chown -R www-data:www-data cache system public/uploads
