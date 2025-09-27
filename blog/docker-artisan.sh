#!/bin/bash

# Run Laravel Artisan commands inside Docker container
COMMAND="$*"

if [ -z "$COMMAND" ]; then
    echo "🎨 Running interactive Artisan shell..."
    docker compose exec app php artisan tinker
else
    echo "🎨 Running Artisan command: $COMMAND"
    docker compose exec app php artisan $COMMAND
fi