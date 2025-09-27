#!/bin/bash

# Show Docker container logs
SERVICE=$1

if [ -z "$SERVICE" ]; then
    echo "📄 Showing logs for all services..."
    docker compose logs -f
else
    echo "📄 Showing logs for service: $SERVICE"
    docker compose logs -f $SERVICE
fi