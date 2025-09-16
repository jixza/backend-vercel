@echo off
REM ====================================================================
REM PG Card MVP - Windows Development Setup Script
REM ====================================================================

echo.
echo ===================================================
echo   PG Card MVP - Windows Development Setup
echo ===================================================
echo.

REM Check if Docker Desktop is running
echo [1/8] Checking Docker Desktop...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Desktop is not running or not installed
    echo Please install Docker Desktop from: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)
echo ✅ Docker Desktop is running

REM Check if Docker Compose is available
echo [2/8] Checking Docker Compose...
docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Compose not found
    pause
    exit /b 1
)
echo ✅ Docker Compose is available

REM Create .env file if not exists
echo [3/8] Setting up environment file...
if not exist .env (
    echo 📝 Creating .env file from .env.example...
    copy .env.example .env
    
    REM Generate app key placeholder
    echo APP_KEY=base64:$(openssl rand -base64 32 ^| tr -d "=+/" ^| cut -c1-32) >> .env.temp
    findstr /v "APP_KEY=" .env > .env.temp2
    type .env.temp2 > .env
    type .env.temp >> .env
    del .env.temp .env.temp2
    
    echo ⚙️ Please edit .env file and set your database credentials
) else (
    echo ✅ .env file already exists
)

REM Stop any running containers
echo [4/8] Stopping existing containers...
docker-compose down 2>nul

REM Build Docker images
echo [5/8] Building Docker images...
docker-compose build --no-cache
if %errorlevel% neq 0 (
    echo ❌ Failed to build Docker images
    pause
    exit /b 1
)
echo ✅ Docker images built successfully

REM Start services
echo [6/8] Starting services...
docker-compose up -d
if %errorlevel% neq 0 (
    echo ❌ Failed to start services
    pause
    exit /b 1
)

REM Wait for services to be ready
echo [7/8] Waiting for services to be ready...
timeout /t 30 /nobreak >nul

REM Run Laravel setup
echo [8/8] Running Laravel setup...
echo 📦 Installing Composer dependencies...
docker-compose exec app composer install --no-dev --optimize-autoloader

echo 🔑 Generating application key...
docker-compose exec app php artisan key:generate --force

echo 🗄️ Running database migrations...
docker-compose exec app php artisan migrate --force

echo 🔗 Creating storage link...
docker-compose exec app php artisan storage:link

echo ⚡ Optimizing application...
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo 👤 Creating admin user...
docker-compose exec app php artisan make:filament-user

REM Health check
echo.
echo 🏥 Running health check...
timeout /t 5 /nobreak >nul
curl -f http://localhost/api/health >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Application is healthy!
) else (
    echo ⚠️ Health check failed, but application might still be starting...
)

echo.
echo ===================================================
echo           🎉 Setup Complete!
echo ===================================================
echo.
echo 🌐 Application URLs:
echo    - Main App: http://localhost
echo    - Admin Panel: http://localhost/admin
echo    - API Health: http://localhost/api/health
echo    - API Docs: http://localhost/api/docs
echo.
echo 🔧 Useful Commands:
echo    - View logs: docker-compose logs -f
echo    - Stop: docker-compose down
echo    - Restart: docker-compose restart
echo    - Shell access: docker-compose exec app bash
echo.
echo 📋 What's Running:
docker-compose ps

echo.
echo 💡 Tips:
echo    - Edit .env file for database settings
echo    - Check docker-compose logs if issues occur
echo    - Use Ctrl+C to stop viewing logs
echo.

pause