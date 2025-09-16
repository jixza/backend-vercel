@echo off
echo 🐳 Starting PG Card MVP with Docker on Windows...

REM Check if Docker Desktop is running
docker version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker is not running. Please start Docker Desktop first.
    pause
    exit /b 1
)

REM Copy environment file if it doesn't exist
if not exist .env (
    echo 📝 Creating .env file from .env.example...
    copy .env.example .env
    echo ⚠️  Please edit .env file to set proper values!
)

echo 🏗️  Building Docker containers...
docker-compose down
docker-compose build --no-cache

echo 🚀 Starting containers...
docker-compose up -d

echo ⏳ Waiting for database to be ready...
timeout /t 30

echo 📦 Installing Composer dependencies...
docker-compose exec app composer install --optimize-autoloader

echo 🔑 Generating application key...
docker-compose exec app php artisan key:generate

echo 🗄️  Running database migrations...
docker-compose exec app php artisan migrate --force

echo 🔗 Creating storage link...
docker-compose exec app php artisan storage:link

echo 🧹 Clearing caches...
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo ⚡ Optimizing for production...
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo.
echo 🎉 Setup complete!
echo.
echo 📋 Application Details:
echo 🌐 Application URL: http://localhost:8000
echo 🗄️  Database: PostgreSQL on localhost:5432
echo 📊 Redis: localhost:6379
echo.
echo 📊 Container Status:
docker-compose ps

echo.
echo 📝 Useful Commands:
echo   View logs: docker-compose logs -f
echo   Restart app: docker-compose restart app
echo   Access shell: docker-compose exec app bash
echo   Run artisan: docker-compose exec app php artisan [command]
echo   Token cleanup: docker-compose exec app php artisan tokens:cleanup

echo.
echo ✅ PG Card MVP is now running!
pause