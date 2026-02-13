#!/bin/bash
set -e

git config core.fileMode false
git config --global --add safe.directory /var/www/html

echo "Installing composer dependencies..."
composer install --no-interaction

echo "Setting permissions..."
chgrp -R www-data /var/www/html
chmod -R g+w /var/www/html

echo "Cleaning Doctrine..."
composer dump-autoload --optimize
php bin/doctrine orm:clear-cache:metadata || true
php bin/doctrine orm:clear-cache:query || true
php bin/doctrine orm:generate-proxies || true

echo "Setup finished successfully!"