#!/usr/bin/env bash
set -e

echo "Starting Deployment for HMS Backend..."

# Enable Maintenance Mode
(php artisan down --secret="hms-staging-bypass" || true)

# Pull latest code
git pull origin main

# Install production dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and rebuild caches
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Run Database Migrations
php artisan migrate --force

# Restart Queue Workers
php artisan queue:restart

# Disable Maintenance Mode
php artisan up

echo "HMS Backend Deployed Successfully!"