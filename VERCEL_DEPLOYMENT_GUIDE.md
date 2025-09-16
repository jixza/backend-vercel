# 🚀 Vercel Deployment Guide - PG Card MVP

Panduan lengkap untuk deploy aplikasi Laravel PG Card MVP ke Vercel dengan konfigurasi production-ready.

## 📋 Prerequisites

### Tools yang Diperlukan:
- **Node.js** (v18 atau lebih baru)
- **PHP** (v8.4 atau lebih baru) 
- **Composer** (v2.x)
- **Git**
- **Vercel CLI**

### External Services yang Direkomendasikan:
- **Database**: PlanetScale (MySQL) atau Railway (PostgreSQL)
- **Cache**: Upstash Redis (optional)
- **Storage**: Cloudinary atau AWS S3 (untuk file uploads)

## 🔧 Setup Instructions

### 1. Install Vercel CLI
```bash
# Global installation
npm install -g vercel

# Atau menggunakan yarn
yarn global add vercel
```

### 2. Prepare Project for Vercel

#### Windows:
```batch
# Jalankan script setup otomatis
scripts\deploy-vercel.bat
```

#### Linux/macOS:
```bash
# Make script executable
chmod +x scripts/deploy-vercel.sh

# Run setup script
./scripts/deploy-vercel.sh
```

### 3. Setup Database

#### Option A: PlanetScale (Recommended)
1. Daftar di [PlanetScale](https://planetscale.com/)
2. Buat database baru
3. Dapatkan connection string
4. Set environment variables:
   ```
   DB_CONNECTION=mysql
   DB_HOST=aws.connect.psdb.cloud
   DB_PORT=3306
   DB_DATABASE=your-database-name
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
   ```

#### Option B: Railway PostgreSQL
1. Daftar di [Railway](https://railway.app/)
2. Deploy PostgreSQL database
3. Dapatkan connection details
4. Set environment variables:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=containers-us-west-xxx.railway.app
   DB_PORT=5432
   DB_DATABASE=railway
   DB_USERNAME=postgres
   DB_PASSWORD=your-railway-password
   ```

### 4. Deploy to Vercel

#### Login ke Vercel:
```bash
vercel login
```

#### Deploy ke Preview (Testing):
```bash
vercel
```

#### Deploy ke Production:
```bash
vercel --prod
```

## 🔐 Environment Variables

Set environment variables di Vercel dashboard atau menggunakan CLI:

### Core Laravel Settings:
```bash
vercel env add APP_NAME production
# Enter: PG Card MVP

vercel env add APP_ENV production
# Enter: production

vercel env add APP_DEBUG production
# Enter: false

vercel env add APP_KEY production
# Enter: base64:your-generated-key-here

vercel env add APP_URL production
# Enter: https://your-app-name.vercel.app
```

### Database Settings:
```bash
vercel env add DB_CONNECTION production
# Enter: mysql (atau pgsql)

vercel env add DB_HOST production
# Enter: your-database-host

vercel env add DB_PORT production
# Enter: 3306 (atau 5432 untuk PostgreSQL)

vercel env add DB_DATABASE production
# Enter: your-database-name

vercel env add DB_USERNAME production
# Enter: your-username

vercel env add DB_PASSWORD production
# Enter: your-password
```

### Laravel Optimizations for Vercel:
```bash
vercel env add LOG_CHANNEL production
# Enter: stderr

vercel env add SESSION_DRIVER production
# Enter: cookie

vercel env add CACHE_STORE production
# Enter: array

vercel env add QUEUE_CONNECTION production
# Enter: sync
```

### Application Specific:
```bash
vercel env add TEMP_TOKEN_EXPIRE_MINUTES production
# Enter: 60

vercel env add TEMP_TOKEN_MAX_USES production
# Enter: 10

vercel env add TEMP_TOKEN_LENGTH production
# Enter: 32
```

## 📁 File Structure untuk Vercel

```
├── api/
│   └── index.php          # Serverless function entry point
├── vercel.json            # Vercel configuration
├── .vercelignore          # Files to ignore during deployment
├── .env.vercel            # Environment template for Vercel
├── scripts/
│   ├── deploy-vercel.sh   # Linux/macOS deployment script
│   └── deploy-vercel.bat  # Windows deployment script
└── config/
    └── database.vercel.php # Database config optimized for Vercel
```

## 🚀 Deployment Process

### 1. Pre-deployment:
```bash
# Install dependencies (production)
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize autoloader
composer dump-autoload --optimize
```

### 2. Deploy:
```bash
# Deploy ke production
vercel --prod

# Atau deploy ke preview untuk testing
vercel
```

### 3. Post-deployment:
- Run database migrations
- Verify environment variables
- Test API endpoints
- Monitor logs

## 🔍 Monitoring & Debugging

### View Deployments:
```bash
vercel ls
```

### View Logs:
```bash
vercel logs your-deployment-url
```

### Local Development:
```bash
vercel dev
```

## ⚠️ Vercel Limitations untuk Laravel

### Yang Tidak Didukung:
- ❌ Background jobs/queues (gunakan `QUEUE_CONNECTION=sync`)
- ❌ Persistent file storage (gunakan cloud storage)
- ❌ Server-side sessions (gunakan `SESSION_DRIVER=cookie`)
- ❌ Cron jobs (gunakan external cron services)
- ❌ WebSockets
- ❌ Long-running processes

### Solusi Alternatif:
- **Queue Jobs**: Gunakan webhook endpoints
- **File Storage**: Cloudinary, AWS S3, atau DigitalOcean Spaces
- **Cron Jobs**: GitHub Actions, external cron services
- **Cache**: Upstash Redis atau in-memory cache
- **Sessions**: Cookie-based sessions

## 🛡️ Security Considerations

### 1. Environment Variables:
- Semua sensitive data di environment variables
- Tidak ada credentials di kode

### 2. Database Security:
- Gunakan SSL connections
- Set proper firewall rules
- Regular security updates

### 3. API Security:
- Rate limiting sudah dikonfigurasi
- Token-based authentication
- CORS protection

## 🔗 Useful Commands

### Vercel Management:
```bash
# List all deployments
vercel ls

# Remove deployment
vercel rm deployment-url

# View project settings
vercel project

# Set custom domain
vercel domains add your-domain.com

# View environment variables
vercel env ls

# Pull environment variables
vercel env pull .env.local
```

### Laravel on Vercel:
```bash
# Generate APP_KEY
php artisan key:generate --show

# Run migrations (locally with production DB)
php artisan migrate --env=production

# Test API endpoints
curl https://your-app.vercel.app/api/health
```

## 📊 Performance Optimization

### 1. Composer Optimization:
```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

### 2. Laravel Caching:
```bash
# Di local sebelum deploy
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Database Optimization:
- Gunakan indexing yang tepat
- Optimize queries
- Gunakan database pooling

## 🐛 Troubleshooting

### Common Issues:

#### 1. "Class not found" errors:
```bash
composer dump-autoload --optimize
```

#### 2. Environment variables not loading:
```bash
# Check if variables are set correctly
vercel env ls
```

#### 3. Database connection issues:
- Verify database credentials
- Check SSL requirements
- Test connection locally

#### 4. 504 Gateway Timeout:
- Optimize database queries
- Reduce processing time
- Check for infinite loops

### Debug Commands:
```bash
# Local testing with Vercel
vercel dev

# View function logs
vercel logs --follow

# Inspect build process
vercel --debug
```

## 📞 Support

### Documentation:
- [Vercel PHP Runtime](https://vercel.com/docs/runtimes#official-runtimes/php)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [PlanetScale Laravel](https://planetscale.com/docs/tutorials/planetscale-laravel)

### Community:
- Vercel Discord
- Laravel Discord
- Stack Overflow

## 🎯 Production Checklist

- [ ] Environment variables configured
- [ ] Database connection tested
- [ ] SSL certificates configured
- [ ] Custom domain set up (optional)
- [ ] Error monitoring enabled
- [ ] Performance monitoring enabled
- [ ] Backup strategy implemented
- [ ] Security headers configured
- [ ] API rate limiting tested
- [ ] Health check endpoint working

---

**Happy Deploying! 🚀**