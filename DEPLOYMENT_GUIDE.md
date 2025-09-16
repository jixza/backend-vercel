# 🚀 PG Card MVP - Setup Lengkap dengan Temporary Token System

## ✅ Yang Sudah Dibuat

### 1. **Docker Configuration**
- ✅ `docker-compose.yml` - dengan Redis, Queue Worker, Scheduler
- ✅ `Dockerfile` - dengan Redis extension dan PostgreSQL support
- ✅ `docker/nginx/nginx.conf` - dengan security headers dan token endpoint rules

### 2. **Database**
- ✅ Migration untuk PostgreSQL dengan inet type untuk IP address
- ✅ Indexes optimized untuk token queries
- ✅ Foreign key constraints

### 3. **Environment Config**
- ✅ `.env.example` updated untuk Redis dan PostgreSQL
- ✅ Token system configuration
- ✅ Queue dan cache configuration

### 4. **Deployment Scripts**
- ✅ `scripts/setup.sh` - Linux/Mac setup script
- ✅ `scripts/setup.bat` - Windows setup script  
- ✅ `scripts/deploy-campus.sh` - Campus deployment script
- ✅ `scripts/api-test.http` - API testing endpoints
- ✅ `DOCKER_README.md` - Complete documentation

### 5. **Previous Files (Already Created)**
- ✅ `app/Models/TemporaryPatientToken.php`
- ✅ `app/Http/Controllers/Api/TemporaryPatientTokenController.php`
- ✅ `app/Console/Commands/CleanupExpiredTokens.php`
- ✅ Updated `routes/api.php`
- ✅ Tests dan documentation

## 🐳 **Cara Deploy dengan Docker**

### **Prerequisites**
1. **Install Docker Desktop**
   - Windows: Download dari https://desktop.docker.com/win/stable/Docker%20Desktop%20Installer.exe
   - Mac: Download dari https://desktop.docker.com/mac/stable/Docker.dmg
   - Linux: `sudo apt install docker.io docker-compose`

2. **Start Docker Desktop** (Windows/Mac)

### **Setup Commands**

#### **Windows (PowerShell)**
```powershell
# Navigate ke project directory
cd "d:\filament\Backend-pg\pg-card-mvp-main"

# Run setup script
.\scripts\setup.bat
```

#### **Linux/Mac**
```bash
# Navigate ke project directory
cd /path/to/pg-card-mvp-main

# Make script executable
chmod +x scripts/setup.sh

# Run setup
./scripts/setup.sh
```

#### **Manual Setup (Jika Script Error)**
```bash
# 1. Copy environment file
cp .env.example .env

# 2. Build dan start containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# 3. Wait for database
sleep 30

# 4. Install dependencies
docker-compose exec app composer install --optimize-autoloader

# 5. Generate key
docker-compose exec app php artisan key:generate

# 6. Run migrations
docker-compose exec app php artisan migrate --force

# 7. Create storage link
docker-compose exec app php artisan storage:link

# 8. Clear caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# 9. Cache for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 🎯 **What's Running After Setup**

### **Services**
- **Application**: http://localhost:8000
- **PostgreSQL**: localhost:5432
- **Redis**: localhost:6379
- **Queue Worker**: Background processing
- **Scheduler**: Token cleanup every hour

### **Containers**
- `pg_card_mvp` - Main Laravel application
- `pg_card_nginx` - Web server
- `pg_card_db` - PostgreSQL database
- `pg_card_redis` - Redis cache
- `pg_card_queue` - Queue worker
- `pg_card_scheduler` - Cron scheduler

## 🧪 **Testing the System**

### **1. Health Check**
```bash
curl http://localhost:8000/api/health
```

### **2. Login to Get Token**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### **3. Generate Temporary Token**
```bash
curl -X POST http://localhost:8000/api/patient/tokens/generate/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "expiration_minutes": 30
  }'
```

### **4. Access Patient Data via Token**
```bash
curl http://localhost:8000/api/patient/token/YOUR_TEMPORARY_TOKEN
```

## 🎓 **Campus Deployment**

### **Server Requirements**
- Ubuntu 20.04+ or CentOS 8+
- Docker & Docker Compose
- 4GB+ RAM
- 20GB+ Storage

### **Deployment Steps**
```bash
# 1. Install Docker (if not installed)
sudo apt update
sudo apt install docker.io docker-compose git

# 2. Clone repository
git clone https://github.com/xinzzu/backend-pg.git
cd backend-pg

# 3. Run campus deployment
chmod +x scripts/deploy-campus.sh
./scripts/deploy-campus.sh

# 4. Configure firewall (if needed)
sudo ufw allow 8000
```

## 🔧 **Management Commands**

```bash
# View logs
docker-compose logs -f

# Restart application
docker-compose restart app

# Access shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan tinker
docker-compose exec app php artisan tokens:cleanup
docker-compose exec app php artisan queue:work

# Database operations
docker-compose exec db psql -U postgres -d pg_card_mvp

# Backup database
docker-compose exec db pg_dump -U postgres pg_card_mvp > backup.sql
```

## 🔒 **Security Features**

### **Implemented Security**
- ✅ Rate limiting pada API endpoints
- ✅ Security headers (XSS, CSRF protection)
- ✅ Token expiration (configurable)
- ✅ One-time use tokens
- ✅ IP address logging
- ✅ User agent tracking
- ✅ Automatic token cleanup

### **Production Security Checklist**
- [ ] Change default database passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules
- [ ] Set up log monitoring
- [ ] Regular security updates
- [ ] Backup strategy

## 📊 **Monitoring & Maintenance**

### **Health Monitoring**
```bash
# Application health
curl http://localhost:8000/api/health

# Container status
docker-compose ps

# Resource usage
docker stats
```

### **Log Monitoring**
```bash
# Application logs
docker-compose logs -f app

# Nginx access logs
docker-compose exec nginx tail -f /var/log/nginx/access.log

# Token access logs
docker-compose exec nginx tail -f /var/log/nginx/token_access.log
```

### **Maintenance Tasks**
```bash
# Weekly token cleanup
docker-compose exec app php artisan tokens:cleanup --days=7

# Monthly log rotation
docker-compose exec app php artisan log:clear

# Database optimization
docker-compose exec db psql -U postgres -d pg_card_mvp -c "VACUUM ANALYZE;"
```

## 🚨 **Troubleshooting**

### **Common Issues**

1. **Port Already in Use**
   ```bash
   # Change ports in docker-compose.yml
   ports:
     - "8001:80"  # Change from 8000 to 8001
   ```

2. **Database Connection Failed**
   ```bash
   # Check database logs
   docker-compose logs db
   
   # Restart database
   docker-compose restart db
   ```

3. **Permission Denied**
   ```bash
   # Fix permissions
   docker-compose exec app chown -R www-data:www-data /var/www/storage
   docker-compose exec app chmod -R 755 /var/www/storage
   ```

4. **Out of Memory**
   ```bash
   # Increase Docker memory limit in Docker Desktop settings
   # Or reduce number of workers in docker-compose.yml
   ```

## 📞 **Support**

Jika ada issues:
1. Check logs: `docker-compose logs -f`
2. Check container status: `docker-compose ps`
3. Restart services: `docker-compose restart`
4. Rebuild if needed: `docker-compose build --no-cache`

---

## 🎉 **Ready to Deploy!**

Sistem PG Card MVP dengan Temporary Token sudah siap untuk deployment!

### **Next Steps:**
1. Install Docker Desktop
2. Run setup script
3. Test API endpoints
4. Deploy ke campus server
5. Configure monitoring

**Sistem ini memberikan security yang kuat untuk QR Code access dengan token yang expire dan one-time use!** 🔐🚀