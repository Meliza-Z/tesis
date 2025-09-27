#!/bin/bash

# Run NPM commands inside Docker container
COMMAND="$*"

if [ -z "$COMMAND" ]; then
    echo "📦 Available NPM commands:"
    echo "   install     - Install dependencies"
    echo "   run dev     - Run development build"
    echo "   run build   - Run production build"
    echo "   run watch   - Watch for changes"
    exit 1
else
    echo "📦 Running NPM command: $COMMAND"
    docker compose exec app npm $COMMAND
fi