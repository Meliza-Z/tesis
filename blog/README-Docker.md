# Docker Setup for Laravel Blog System

This Laravel project is now configured to run with Docker, providing a### Fix storage permissions

```bash
# Fix storage permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Rebuild Containers

```bash
# Clean rebuild
docker compose down
docker compose up -d --build --force-recreate
```

### View Container Status

````bash
docker compose ps
```pment environment without needing Laragon or local PHP/MySQL installations.

## Prerequisites

- Docker Desktop (latest version)
- Docker Compose (included with Docker Desktop)

## Quick Start

### 1. Initial Setup
```bash
# Run the automated setup script
./docker-setup.sh
````

This script will:

-   Create a `.env` file from the Docker template
-   Build Docker containers
-   Start all services
-   Run database migrations
-   Set proper permissions

### 2. Manual Setup (Alternative)

If you prefer manual setup:

```bash
# Copy environment file
cp .env.docker .env

# Build and start containers
docker compose up -d --build

# Generate application key
docker compose exec app php artisan key:generate

# Run migrations
docker compose exec app php artisan migrate --seed
```

## Available Services

Once running, you can access:

-   **Laravel Application**: http://localhost:8000
-   **phpMyAdmin**: http://localhost:8080
    -   Server: `db`
    -   Username: `laravel_user`
    -   Password: `laravel_password`
-   **MailHog** (Email testing): http://localhost:8025

## Helper Scripts

### Starting/Stopping

```bash
./docker-start.sh    # Start all containers
./docker-stop.sh     # Stop all containers
```

### Development Commands

```bash
./docker-artisan.sh [command]     # Run Artisan commands
./docker-composer.sh [command]    # Run Composer commands
./docker-npm.sh [command]         # Run NPM commands
./docker-logs.sh [service]        # View logs
```

### Examples

```bash
# Run migrations
./docker-artisan.sh migrate

# Install new Composer package
./docker-composer.sh require package/name

# Build frontend assets
./docker-npm.sh run build

# Watch for frontend changes
./docker-npm.sh run dev

# View application logs
./docker-logs.sh app
```

## Container Architecture

-   **app**: PHP 8.2 with Apache, your Laravel application
-   **db**: MySQL 8.0 database
-   **phpmyadmin**: Database management interface
-   **redis**: Redis for caching and sessions
-   **mailhog**: Email testing service

## Database Connection

The application connects to the database using these Docker-specific settings:

-   Host: `db`
-   Database: `blog`
-   Username: `laravel_user`
-   Password: `laravel_password`

## Troubleshooting

### Port Conflicts

If you get port conflict errors, stop other services:

-   Port 8000: Other web servers
-   Port 3306: Local MySQL
-   Port 8080: Other phpMyAdmin instances

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Rebuild Containers

```bash
# Clean rebuild
docker-compose down
docker-compose up -d --build --force-recreate
```

### View Container Status

```bash
docker-compose ps
```

## Development Workflow

1. Start containers: `./docker-start.sh`
2. Make code changes (files are synced automatically)
3. Run Artisan commands: `./docker-artisan.sh [command]`
4. View logs: `./docker-logs.sh`
5. Stop containers: `./docker-stop.sh`

## Production Notes

For production deployment:

1. Update `.env` with production settings
2. Set `APP_ENV=production` and `APP_DEBUG=false`
3. Use proper secrets for passwords
4. Consider using Docker Swarm or Kubernetes
5. Set up proper SSL/TLS termination

## Benefits over Laragon

-   **Consistency**: Same environment across all developers
-   **Isolation**: No conflicts with system PHP/MySQL
-   **Portability**: Works on Windows, Mac, and Linux
-   **Version Control**: Docker configuration is versioned
-   **Easy Setup**: One command setup for new team members
