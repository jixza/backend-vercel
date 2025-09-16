#!/bin/bash

# Campus Deployment Script - Enhanced Version
# Author: Campus IT Team
# Description: Deploy Laravel app dengan Docker untuk server kampus

set -e  # Exit on any error

echo "🎓 Starting Campus Server Deployment..."

# Check if running as root or with sudo
if [ "$EUID" -eq 0 ]; then
    echo "⚠️  Please don't run as root. Use sudo when needed."
    exit 1
fi

# Configuration
PROJECT_NAME="pg-card-mvp"
PROJECT_DIR="/opt/${PROJECT_NAME}"
BACKUP_DIR="/var/backups/${PROJECT_NAME}"
LOG_FILE="/var/log/${PROJECT_NAME}/deploy.log"
REPO_URL="https://github.com/xinzzu/backend-pg.git"  # Update dengan URL repo yang benar
USER=$(whoami)

# Create necessary directories
echo "📁 Creating directories..."
sudo mkdir -p "${BACKUP_DIR}"
sudo mkdir -p "/var/log/${PROJECT_NAME}"
sudo mkdir -p "/etc/${PROJECT_NAME}"
sudo mkdir -p "${PROJECT_DIR}"

# Set ownership
sudo chown -R $USER:$USER $PROJECT_DIR
sudo chown -R $USER:$USER $BACKUP_DIR

cd $PROJECT_DIR

# Backup existing deployment
if [ -d ".git" ]; then
    echo "📦 Creating backup..."
    sudo tar -czf $BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).tar.gz . || echo "Backup failed, continuing..."
fi

# Update code
if [ -d ".git" ]; then
    echo "📥 Updating repository..."
    git pull origin main
else
    echo "📥 Cloning repository..."
    git clone $REPO_URL .
fi

# Set permissions for scripts
chmod +x scripts/setup.sh

# Copy production environment
if [ ! -f .env ]; then
    echo "📝 Creating production .env file..."
    cp .env.example .env
    
    # Update some production values
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    sed -i 's/LOG_LEVEL=debug/LOG_LEVEL=error/' .env
    
    echo "⚠️  Please edit .env file for production values:"
    echo "   - DB_PASSWORD"
    echo "   - APP_KEY (will be generated)"
    echo "   - MAIL settings"
    read -p "Press Enter to continue after editing .env..."
fi

# Deploy with Docker
echo "🐳 Starting deployment with Docker..."
./scripts/setup.sh

# Setup monitoring
echo "📊 Setting up monitoring..."
docker-compose exec app php artisan queue:restart

# Test deployment
echo "🧪 Testing deployment..."
sleep 10

# Test health endpoint
if curl -f http://localhost:8000/api/health &> /dev/null; then
    echo "✅ Health check passed"
else
    echo "❌ Health check failed"
fi

# Test basic API
if curl -f http://localhost:8000/api/auth/login &> /dev/null; then
    echo "✅ API endpoints accessible"
else
    echo "❌ API endpoints not accessible"
fi

echo ""
echo "🎉 Campus deployment complete!"
echo ""
echo "📋 Next Steps:"
echo "1. Configure firewall to allow port 8000"
echo "2. Set up SSL certificate for HTTPS"
echo "3. Configure domain name"
echo "4. Set up log rotation"
echo "5. Configure backup schedule"
echo ""
echo "📊 Monitoring Commands:"
echo "  Application logs: docker-compose logs -f app"
echo "  Database logs: docker-compose logs -f db"
echo "  Nginx logs: docker-compose logs -f nginx"
echo "  System status: docker-compose ps"
echo ""
echo "🔧 Maintenance Commands:"
echo "  Restart services: docker-compose restart"
echo "  Update application: git pull && docker-compose restart app"
echo "  Backup database: docker-compose exec db pg_dump -U postgres pg_card_mvp > backup.sql"
echo "  Clean expired tokens: docker-compose exec app php artisan tokens:cleanup"

echo ""
echo "🌐 Application accessible at: http://$(hostname -I | awk '{print $1}'):8000"