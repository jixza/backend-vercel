#!/bin/bash

# ====================================================================
# Vercel Deployment Script for Laravel PG Card MVP
# ====================================================================

echo "🚀 Starting Vercel deployment preparation..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if Vercel CLI is installed
if ! command -v vercel &> /dev/null; then
    echo -e "${YELLOW}📦 Installing Vercel CLI...${NC}"
    npm install -g vercel
fi

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer is not installed. Please install Composer first.${NC}"
    exit 1
fi

# Install PHP dependencies
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# Generate optimized autoload files
echo -e "${YELLOW}⚡ Optimizing autoloader...${NC}"
composer dump-autoload --optimize --no-dev

# Clear and cache Laravel configurations
echo -e "${YELLOW}🔧 Optimizing Laravel...${NC}"
php artisan config:clear || echo "Config clear skipped (no .env found)"
php artisan route:clear || echo "Route clear skipped"
php artisan view:clear || echo "View clear skipped"

# Create .vercelignore file
echo -e "${YELLOW}📝 Creating .vercelignore...${NC}"
cat > .vercelignore << 'EOF'
# Laravel specific
/node_modules
/vendor
/storage/logs
/storage/app
/storage/framework
/bootstrap/cache
/.env
/.env.local
/.env.production
/.env.staging
/.env.testing

# Development files
.phpunit.result.cache
/tests
/docker
/docker-compose.yml
/docker-compose.prod.yml
/Dockerfile
/build.sh
/scripts

# Version control
/.git
/.gitignore
/.gitattributes

# IDE
/.vscode
/.idea
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Logs
*.log
npm-debug.log*
yarn-debug.log*
yarn-error.log*

# Dependencies
/bower_components

# Build artifacts
/dist
/build
/public/hot
/public/storage
/storage/*.key

# Database
*.sqlite
*.sqlite3
*.db

# Temporary files
/tmp
/temp
EOF

# Create build script for Vercel
echo -e "${YELLOW}📝 Creating build script...${NC}"
cat > build.sh << 'EOF'
#!/bin/bash

echo "🏗️  Building Laravel for Vercel..."

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Clear caches
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Create necessary directories
mkdir -p bootstrap/cache
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Set proper permissions (if on Unix-like system)
if [ "$VERCEL_ENV" != "production" ]; then
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
fi

echo "✅ Build completed successfully"
EOF

chmod +x build.sh

# Display deployment instructions
echo -e "${GREEN}✅ Vercel deployment prepared successfully!${NC}"
echo ""
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. Login to Vercel: ${GREEN}vercel login${NC}"
echo "2. Deploy: ${GREEN}vercel --prod${NC}"
echo ""
echo -e "${YELLOW}🔧 Environment Variables to set in Vercel dashboard:${NC}"
echo "- APP_NAME=PG Card MVP"
echo "- APP_ENV=production"
echo "- APP_KEY=(generate with: php artisan key:generate --show)"
echo "- APP_DEBUG=false"
echo "- APP_URL=https://your-app-name.vercel.app"
echo "- DB_CONNECTION=mysql (or pgsql)"
echo "- DB_HOST=your-database-host"
echo "- DB_PORT=3306 (or 5432 for PostgreSQL)"
echo "- DB_DATABASE=your-database-name"
echo "- DB_USERNAME=your-username"
echo "- DB_PASSWORD=your-password"
echo "- LOG_CHANNEL=stderr"
echo "- SESSION_DRIVER=cookie"
echo "- CACHE_STORE=array"
echo "- QUEUE_CONNECTION=sync"
echo ""
echo -e "${YELLOW}💡 Tips:${NC}"
echo "- Use PlanetScale or Railway for database"
echo "- Use Upstash Redis for caching (optional)"
echo "- Use Cloudinary or AWS S3 for file storage"
echo "- Test locally with: ${GREEN}vercel dev${NC}"
echo ""
echo -e "${YELLOW}🔗 Useful commands:${NC}"
echo "- Deploy to preview: ${GREEN}vercel${NC}"
echo "- Deploy to production: ${GREEN}vercel --prod${NC}"
echo "- View deployments: ${GREEN}vercel ls${NC}"
echo "- View logs: ${GREEN}vercel logs${NC}"
echo "- Set environment variables: ${GREEN}vercel env add APP_KEY production${NC}"