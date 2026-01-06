# Runbook - Oxford Plataforma

## 🚀 Deployment

### Deploy to Staging
```bash
# Merge to main triggers CI
git push origin main

# Manual deploy
./scripts/deploy.sh staging
```

### Deploy to Production
```bash
# After staging verification
./scripts/deploy.sh production
```

### Rollback
```bash
# Immediate rollback to previous version
kubectl rollout undo deployment/oxford-backend -n production
kubectl rollout undo deployment/oxford-frontend -n production
kubectl rollout undo deployment/oxford-ai -n production
```

---

## 🔧 Common Operations

### Restart Services
```bash
# Via Docker
docker-compose restart backend frontend ai_service

# Via Kubernetes
kubectl rollout restart deployment/oxford-backend
```

### View Logs
```bash
# Docker logs
docker-compose logs -f backend

# Kubernetes logs
kubectl logs -f deployment/oxford-backend -n production

# Application logs
tail -f backend/symfony/var/log/prod.log
```

### Clear Cache
```bash
# Symfony cache
cd backend/symfony
php bin/console cache:clear

# Redis cache
redis-cli FLUSHALL

# Frontend cache
cd frontend && npm run build
```

### Database Operations
```bash
# Run migrations
php bin/console doctrine:migrations:migrate

# Rollback migration
php bin/console doctrine:migrations:migrate prev

# Create backup
pg_dump oxford_db > backup_$(date +%Y%m%d).sql

# Restore backup
psql oxford_db < backup_20260105.sql
```

---

## 🚨 Troubleshooting

### Service Not Responding

1. Check if service is running:
```bash
docker-compose ps
# or
kubectl get pods -n production
```

2. Check logs for errors:
```bash
docker-compose logs backend | tail -100
```

3. Verify connections:
```bash
# Database
php bin/console doctrine:query:sql "SELECT 1"

# Redis
redis-cli ping

# AI Service
curl http://localhost:8001/health
```

### High CPU/Memory

1. Check resource usage:
```bash
docker stats
# or
kubectl top pods -n production
```

2. Check for stuck processes:
```bash
ps aux | grep php
```

3. Restart affected service:
```bash
docker-compose restart backend
```

### Database Connection Issues

1. Check database status:
```bash
pg_isready -h localhost -p 5432
```

2. Verify connection string:
```bash
echo $DATABASE_URL
```

3. Check max connections:
```sql
SELECT count(*) FROM pg_stat_activity;
```

### JWT Token Issues

1. Verify environment variables:
```bash
echo $JWT_SECRET_KEY
echo $JWT_PUBLIC_KEY
```

2. Regenerate keys:
```bash
php bin/console lexik:jwt:generate-keypair --overwrite
```

### AI Service Errors

1. Check circuit breaker state:
```bash
curl http://localhost:8001/health | jq .circuit_breaker
```

2. Reset if needed:
```bash
curl -X POST http://localhost:8001/admin/reset-circuit-breaker
```

3. Check ML models:
```bash
ls -la backend/ai_service/models/
```

---

## 📊 Monitoring

### Health Checks
```bash
# All services
curl http://localhost:8000/health
curl http://localhost:8001/health
curl http://localhost:5173

# Database
pg_isready

# Redis
redis-cli ping
```

### Metrics
- Grafana: http://localhost:3000
- Prometheus: http://localhost:9090

### Alerts
Configured in Prometheus/Alertmanager:
- Service down > 1 min
- Error rate > 5%
- Response time > 2s
- CPU > 80% for 5 min
- Disk > 90%

---

## 🔐 Security

### Rotate Secrets
```bash
# 1. Generate new secrets
openssl rand -hex 32 > new_secret

# 2. Update in vault/secrets manager
vault kv put secret/oxford jwt_secret=@new_secret

# 3. Restart services
docker-compose restart
```

### Security Incident Response
1. **Contain**: Disable affected user accounts
2. **Investigate**: Check audit logs
3. **Remediate**: Apply fixes
4. **Notify**: Inform affected users
5. **Document**: Post-mortem report

---

## 🔄 Backup & Recovery

### Automated Backups
- Database: Daily at 2 AM
- Files: Daily at 3 AM
- Retention: 30 days

### Manual Backup
```bash
./scripts/backup.sh
```

### Disaster Recovery
1. **RTO**: 1 hour
2. **RPO**: 1 hour

Recovery steps:
1. Provision new infrastructure
2. Restore database from backup
3. Deploy latest code
4. Verify functionality
5. Update DNS

---

## 📞 Contacts

| Role | Name | Contact |
|------|------|---------|
| DevOps Lead | - | devops@oxford.edu.gt |
| Backend Lead | - | backend@oxford.edu.gt |
| On-call | PagerDuty | - |
