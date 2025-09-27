#!/bin/bash

# Simple wait for database to be ready
echo "Waiting for database connection..."
sleep 5
echo "Database should be ready!"

# Generate application key if it doesn't exist
if ! grep -q "APP_KEY=" .env; then
    php artisan key:generate --force
fi

# Run database migrations
php artisan migrate --force

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Application is ready!"

# Execute the original command
exec "$@"