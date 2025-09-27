#!/bin/bash

# Run Composer commands inside Docker container
COMMAND="$*"

if [ -z "$COMMAND" ]; then
    echo "📦 Available Composer commands:"
    echo "   install     - Install dependencies"
    echo "   update      - Update dependencies" 
    echo "   require     - Add new package"
    echo "   remove      - Remove package"
    echo "   dump-autoload - Regenerate autoloader"
    exit 1
else
    echo "📦 Running Composer command: $COMMAND"
    docker compose exec app composer $COMMAND
fi