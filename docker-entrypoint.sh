#!/bin/bash
set -e

chmod -R 777 /var/www/html/cache || true

composer dump-autoload --optimize
php bin/doctrine orm:clear-cache:metadata || true
php bin/doctrine orm:clear-cache:query || true
php bin/doctrine orm:generate-proxies || true

# Starte Apache (das Original-CMD)
exec apache2-foreground
