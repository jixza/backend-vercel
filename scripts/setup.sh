#!/bin/bash

echo "🐳 Starting PG Card MVP with Docker..."

# Check if Docker and Docker Compose are installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  Please edit .env file to set proper values for production!"
fi

echo "🏗️  Building Docker containers..."
docker-compose down
docker-compose build --no-cache

echo "🚀 Starting containers..."
docker-compose up -d

echo "⏳ Waiting for database to be ready..."
sleep 30

# Check if database is ready
echo "🔍 Checking database connection..."
until docker-compose exec -T db pg_isready -U postgres; do
  echo "Waiting for PostgreSQL..."
  sleep 5
done

echo "✅ Database is ready!"

echo "📦 Installing Composer dependencies..."
docker-compose exec app composer install --optimize-autoloader

echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

echo "🧹 Clearing caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo "⚡ Optimizing for production..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "🎉 Setup complete!"
echo ""
echo "📋 Application Details:"
echo "🌐 Application URL: http://localhost:8000"
echo "🗄️  Database: PostgreSQL on localhost:5432"
echo "📊 Redis: localhost:6379"
echo ""
echo "📊 Container Status:"
docker-compose ps

echo ""
echo "📝 Useful Commands:"
echo "  View logs: docker-compose logs -f"
echo "  Restart app: docker-compose restart app"
echo "  Access shell: docker-compose exec app bash"
echo "  Run artisan: docker-compose exec app php artisan [command]"
echo "  Token cleanup: docker-compose exec app php artisan tokens:cleanup"

echo ""
echo "✅ PG Card MVP is now running!"