#!/bin/bash

# Start Docker containers
echo "🚀 Starting Docker containers..."
docker compose up -d

echo "✅ Containers started successfully!"
echo ""
echo "📋 Available services:"
echo "   🌐 Laravel App:     http://localhost:8000"
echo "   🗄️  phpMyAdmin:     http://localhost:8080" 
echo "   📧 MailHog:         http://localhost:8025"
echo ""
echo "📊 Container status:"
docker compose ps