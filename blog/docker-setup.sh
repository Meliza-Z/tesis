#!/bin/bash

# Docker Setup Script for Laravel Blog System
echo "🐳 Setting up Docker environment for Laravel Blog System"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is available
if ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose is not available. Please install Docker Desktop with Compose support."
    exit 1
fi

# Create .env file from Docker template if it doesn't exist
if [ ! -f .env ]; then
    echo "📄 Creating .env file from Docker template..."
    cp .env.docker .env
    echo "✅ .env file created"
else
    echo "⚠️  .env file already exists. You may want to check Docker-specific settings."
fi

# Stop any existing containers
echo "🛑 Stopping existing containers..."
docker compose down

# Build and start the containers
echo "🏗️  Building and starting Docker containers..."
docker compose up -d --build

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Generate application key
echo "🔑 Generating application key..."
docker compose exec app php artisan key:generate

# Run migrations
echo "🗄️  Running database migrations..."
docker compose exec app php artisan migrate --seed

# Set proper permissions
echo "📁 Setting proper permissions..."
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo ""
echo "🎉 Docker setup completed successfully!"
echo ""
echo "📋 Available services:"
echo "   🌐 Laravel App:     http://localhost:8000"
echo "   🗄️  phpMyAdmin:     http://localhost:8080"
echo "   📧 MailHog:         http://localhost:8025"
echo ""
echo "💡 Useful commands:"
echo "   Start:              ./docker-start.sh"
echo "   Stop:               ./docker-stop.sh"
echo "   View logs:          ./docker-logs.sh"
echo "   Run Artisan:        ./docker-artisan.sh [command]"
echo ""