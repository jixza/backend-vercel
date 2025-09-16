# PG Card MVP - Docker Setup

## Requirements
- Docker Desktop (Windows/Mac) or Docker + Docker Compose (Linux)
- Git
- Minimum 4GB RAM
- Port 8000, 5432, 6379 available

## Quick Start

### Windows
```bat
# Run setup script
scripts\setup.bat
```

### Linux/Mac
```bash
# Make script executable
chmod +x scripts/setup.sh

# Run setup
./scripts/setup.sh
```

## Manual Setup

1. **Clone Repository**
   ```bash
   git clone <your-repo-url>
   cd pg-card-mvp-main
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   # Edit .env with your values
   ```

3. **Start Services**
   ```bash
   docker-compose up -d
   ```

4. **Initialize Application**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan storage:link
   ```

## Services

- **Application**: http://localhost:8000
- **Database**: PostgreSQL on localhost:5432
- **Redis**: localhost:6379
- **Queue Worker**: Background job processing
- **Scheduler**: Automatic token cleanup

## API Endpoints

### Health Check
```
GET /api/health
```

### Token Management
```
POST /api/patient/tokens/generate/{patientId}
GET /api/patient/token/{token}
GET /api/patient/tokens/active
DELETE /api/patient/tokens/revoke/{token}
DELETE /api/patient/tokens/revoke-all
```

## Development Commands

```bash
# View logs
docker-compose logs -f

# Access container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tokens:cleanup

# Restart services
docker-compose restart

# Stop all services
docker-compose down
```

## Production Deployment

### Campus Server
```bash
# Run deployment script
chmod +x scripts/deploy-campus.sh
./scripts/deploy-campus.sh
```

### Manual Production Setup
1. Update .env for production values
2. Set APP_ENV=production
3. Set APP_DEBUG=false
4. Configure proper database credentials
5. Set up SSL/HTTPS
6. Configure firewall rules

## Monitoring

### Container Status
```bash
docker-compose ps
```

### Application Logs
```bash
docker-compose logs -f app
```

### Database Logs
```bash
docker-compose logs -f db
```

### Token Cleanup
```bash
docker-compose exec app php artisan tokens:cleanup
```

## Backup

### Database Backup
```bash
docker-compose exec db pg_dump -U postgres pg_card_mvp > backup.sql
```

### Application Backup
```bash
tar -czf backup-$(date +%Y%m%d).tar.gz . --exclude=vendor --exclude=node_modules
```

## Troubleshooting

### Container Issues
```bash
# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Database Connection Issues
```bash
# Check database logs
docker-compose logs db

# Check if database is ready
docker-compose exec db pg_isready -U postgres
```

### Permission Issues
```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 755 /var/www/storage
```

### Port Conflicts
- Change ports in docker-compose.yml if needed
- Default ports: 8000 (nginx), 5432 (postgres), 6379 (redis)

## Security Notes

- Change default database passwords
- Use environment variables for sensitive data
- Enable HTTPS in production
- Configure firewall rules
- Regular security updates
- Monitor access logs