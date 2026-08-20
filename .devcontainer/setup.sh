#!/bin/bash
set -e

git config --global --add safe.directory /workspace
git config core.fileMode false

echo "Installing composer dependencies..."
composer install --no-interaction

echo "Setting permissions..."
chgrp -R www-data /workspace
chmod -R g+w /workspace

echo "Cleaning Doctrine..."
composer dump-autoload --optimize
php bin/doctrine orm:clear-cache:metadata || true
php bin/doctrine orm:clear-cache:query || true
php bin/doctrine orm:generate-proxies || true

echo "Installing npm dependencies for Playwright E2E tests..."
npm install

echo "Installing Playwright browser (Chromium)..."
npx playwright install --with-deps chromium

echo "Setup finished successfully!"