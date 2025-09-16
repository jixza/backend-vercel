#!/bin/bash

# Vercel Build Script untuk Laravel
# Script ini akan dijalankan selama proses build di Vercel

echo "🏗️ Starting Vercel build process..."

# Set proper environment
export NODE_ENV=production
export APP_ENV=production

echo "📦 Installing Composer dependencies..."
# Install Composer dependencies optimized for production
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "🔧 Setting up Laravel environment..."
# Copy environment file if not exists
if [ ! -f .env ]; then
    echo "📝 Copying .env.vercel to .env..."
    cp .env.vercel .env || cp .env.example .env
fi

echo "⚡ Optimizing Laravel..."
# Clear caches (ignore errors in case artisan isn't available yet)
php artisan config:clear 2>/dev/null || echo "Config clear skipped"
php artisan route:clear 2>/dev/null || echo "Route clear skipped"
php artisan view:clear 2>/dev/null || echo "View clear skipped"

# Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p bootstrap/cache
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Set proper permissions (if needed)
echo "🔐 Setting permissions..."
chmod -R 755 storage 2>/dev/null || echo "Permission setting skipped"
chmod -R 755 bootstrap/cache 2>/dev/null || echo "Bootstrap cache permission skipped"

echo "✅ Vercel build completed successfully!"