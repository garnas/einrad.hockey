# Dockerfile
FROM php:8.3-apache

# Installiere notwendige Systempakete und Git
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    git \
    # Cleanup, um das Image klein zu halten
    && rm -rf /var/lib/apt/lists/*

# Installiere notwendige PHP Erweiterungen (GD, PDO_MySQL, ZIP) und Xdebug
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Aktiviere Apache Module headers & rewrite
RUN a2enmod headers rewrite

# Kopiere Ihre eigene Apache Konfiguration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Setze das Arbeitsverzeichnis. Der Code wird später hierhin gemountet.
WORKDIR /var/www/html

# Composer wird erst beim Dev Container Start ausgeführt, nicht hier
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 80 für Web-Traffic
EXPOSE 80

# Ihr existierendes Entrypoint-Skript
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]