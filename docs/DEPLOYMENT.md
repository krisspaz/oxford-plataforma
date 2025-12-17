# Sistema Oxford - Deployment Guide

## Prerequisites

- Docker 24.0+
- Docker Compose 2.0+
- Git
- Domain name (for production)
- SSL certificate (Let's Encrypt recommended)

---

## Development Setup

### 1. Clone Repository
```bash
git clone https://github.com/krisspaz/oxford-plataforma.git
cd oxford-plataforma
```

### 2. Environment Configuration
```bash
cp .env.example .env
# Edit .env with your values
```

### 3. Start Services
```bash
docker-compose up -d
```

### 4. Initialize Database
```bash
docker-compose exec backend php bin/console doctrine:migrations:migrate
docker-compose exec backend php bin/console doctrine:fixtures:load
```

### 5. Access Application
- Frontend: http://localhost:8000
- API: http://localhost:8000/api
- Health: http://localhost:8000/health

---

## Production Deployment

### 1. Server Requirements
- 4GB RAM minimum
- 2 CPU cores
- 50GB SSD storage
- Ubuntu 22.04 LTS recommended

### 2. Install Docker
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
```

### 3. Configure Environment
```bash
# Create production .env
cp .env.example .env.prod

# CRITICAL: Update these values
POSTGRES_PASSWORD=<strong_password>
APP_SECRET=<32_char_random_string>
APP_ENV=prod
JWT_PASSPHRASE=<secure_passphrase>
```

### 4. SSL/TLS Setup (Let's Encrypt)
```bash
# Install certbot
sudo apt install certbot

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com

# Copy certificates
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem ./certs/
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem ./certs/
```

### 5. Update Nginx for HTTPS
Create `backend/nginx/production.conf`:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /etc/nginx/certs/fullchain.pem;
    ssl_certificate_key /etc/nginx/certs/privkey.pem;
    
    # ... rest of config
}
```

### 6. Deploy
```bash
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

### 7. Database Migration
```bash
docker-compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
```

---

## Backup Strategy

### Automated Backups
```bash
# Add to crontab
0 2 * * * /path/to/scripts/backup.sh >> /var/log/oxford-backup.log 2>&1
```

### Manual Backup
```bash
./scripts/backup.sh
```

### Restore
```bash
./scripts/restore.sh /path/to/backup.sql.gz
```

---

## Monitoring

### Health Checks
```bash
curl http://localhost:8000/health
```

### Container Status
```bash
docker-compose ps
docker-compose logs -f
```

### Database Connection
```bash
docker-compose exec database pg_isready
```

---

## Troubleshooting

### Database Connection Issues
```bash
docker-compose exec backend php bin/console doctrine:database:create
docker-compose restart backend
```

### Clear Cache
```bash
docker-compose exec backend php bin/console cache:clear
```

### View Logs
```bash
docker-compose logs backend
docker-compose logs ai_service
docker-compose logs nginx
```

### Rebuild Containers
```bash
docker-compose down
docker-compose up -d --build
```

---

## Security Checklist

- [ ] Change all default passwords
- [ ] Enable HTTPS
- [ ] Configure firewall (allow only 80, 443, 22)
- [ ] Set up fail2ban
- [ ] Enable automatic security updates
- [ ] Configure backup retention
- [ ] Test restore procedure
- [ ] Enable 2FA for admin accounts
- [ ] Review rate limiting settings
- [ ] Set up log monitoring

---

## Performance Tuning

### PostgreSQL
```sql
-- In postgresql.conf
shared_buffers = 256MB
effective_cache_size = 1GB
maintenance_work_mem = 128MB
```

### PHP-FPM
```ini
; In php-fpm.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

### Nginx
```nginx
# In nginx.conf
worker_processes auto;
worker_connections 1024;
keepalive_timeout 65;
gzip on;
```
