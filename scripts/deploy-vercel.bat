@echo off
REM ====================================================================
REM Vercel Deployment Script for Laravel PG Card MVP (Windows)
REM ====================================================================

echo.
echo ============================================
echo   PG Card MVP - Vercel Deployment Setup
echo ============================================
echo.

REM Check if Node.js is installed
echo [1/6] Checking Node.js...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js is not installed
    echo Please install Node.js from: https://nodejs.org/
    pause
    exit /b 1
)
echo ✅ Node.js is installed

REM Check if Composer is installed
echo [2/6] Checking Composer...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Composer is not installed
    echo Please install Composer from: https://getcomposer.org/
    pause
    exit /b 1
)
echo ✅ Composer is installed

REM Install Vercel CLI if not exists
echo [3/6] Checking Vercel CLI...
vercel --version >nul 2>&1
if %errorlevel% neq 0 (
    echo 📦 Installing Vercel CLI...
    npm install -g vercel
    if %errorlevel% neq 0 (
        echo ❌ Failed to install Vercel CLI
        pause
        exit /b 1
    )
)
echo ✅ Vercel CLI is ready

REM Install Composer dependencies
echo [4/6] Installing Composer dependencies...
composer install --no-dev --optimize-autoloader --no-interaction
if %errorlevel% neq 0 (
    echo ❌ Failed to install Composer dependencies
    pause
    exit /b 1
)
echo ✅ Dependencies installed

REM Optimize Laravel
echo [5/6] Optimizing Laravel...
php artisan config:clear 2>nul
php artisan route:clear 2>nul
php artisan view:clear 2>nul
composer dump-autoload --optimize --no-dev
echo ✅ Laravel optimized

REM Create .vercelignore
echo [6/6] Creating .vercelignore...
(
echo # Laravel specific
echo /node_modules
echo /vendor
echo /storage/logs
echo /storage/app
echo /storage/framework
echo /bootstrap/cache
echo /.env
echo /.env.local
echo /.env.production
echo /.env.staging
echo /.env.testing
echo.
echo # Development files
echo .phpunit.result.cache
echo /tests
echo /docker
echo /docker-compose.yml
echo /docker-compose.prod.yml
echo /Dockerfile
echo /build.sh
echo /scripts
echo.
echo # Version control
echo /.git
echo /.gitignore
echo /.gitattributes
echo.
echo # IDE
echo /.vscode
echo /.idea
echo *.swp
echo *.swo
echo.
echo # OS
echo .DS_Store
echo Thumbs.db
echo.
echo # Logs
echo *.log
echo npm-debug.log*
echo yarn-debug.log*
echo yarn-error.log*
echo.
echo # Dependencies
echo /bower_components
echo.
echo # Build artifacts
echo /dist
echo /build
echo /public/hot
echo /public/storage
echo /storage/*.key
echo.
echo # Database
echo *.sqlite
echo *.sqlite3
echo *.db
echo.
echo # Temporary files
echo /tmp
echo /temp
) > .vercelignore

echo ✅ .vercelignore created

echo.
echo ============================================
echo           🎉 Setup Complete!
echo ============================================
echo.
echo 📋 Next Steps:
echo.
echo 1. Login to Vercel:
echo    vercel login
echo.
echo 2. Deploy to preview:
echo    vercel
echo.
echo 3. Deploy to production:
echo    vercel --prod
echo.
echo 🔧 Environment Variables to set in Vercel:
echo    - APP_NAME=PG Card MVP
echo    - APP_ENV=production
echo    - APP_KEY=^(generate with: php artisan key:generate --show^)
echo    - APP_DEBUG=false
echo    - APP_URL=https://your-app-name.vercel.app
echo    - DB_CONNECTION=mysql ^(or pgsql^)
echo    - DB_HOST=your-database-host
echo    - DB_PORT=3306 ^(or 5432 for PostgreSQL^)
echo    - DB_DATABASE=your-database-name
echo    - DB_USERNAME=your-username
echo    - DB_PASSWORD=your-password
echo    - LOG_CHANNEL=stderr
echo    - SESSION_DRIVER=cookie
echo    - CACHE_STORE=array
echo    - QUEUE_CONNECTION=sync
echo.
echo 💡 Recommended Services:
echo    - Database: PlanetScale ^(MySQL^) or Railway ^(PostgreSQL^)
echo    - Cache: Upstash Redis ^(optional^)
echo    - Storage: Cloudinary or AWS S3
echo.
echo 🔗 Useful Commands:
echo    - View deployments: vercel ls
echo    - View logs: vercel logs
echo    - Set env vars: vercel env add APP_KEY production
echo    - Local development: vercel dev
echo.

pause