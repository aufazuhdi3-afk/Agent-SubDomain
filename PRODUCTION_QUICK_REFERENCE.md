# Production Quick Reference

## 🚀 Quick Deployment (TL;DR)

```bash
# On production server:
cd /var/www/unnar-domains

# 1. Install dependencies (5 min)
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 2. Setup environment (2 min)
cp .env.production .env
nano .env  # Edit with production values

# 3. Setup database (5 min)
chmod +x scripts/setup-production-db.sh
sudo ./scripts/setup-production-db.sh
php artisan migrate --env=production --force
php artisan db:seed --env=production  # optional

# 4. Configure web server (10 min - choose one)
# Option A: Nginx (recommended)
sudo cp config/nginx-production.conf /etc/nginx/sites-available/unnar-domains
sudo ln -s /etc/nginx/sites-available/unnar-domains /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# Option B: Apache
sudo cp config/apache-production.conf /etc/apache2/sites-available/unnar-domains.conf
sudo a2ensite unnar-domains
sudo a2enmod rewrite ssl http2
sudo apache2ctl configtest && sudo systemctl restart apache2

# 5. SSL Certificate (5 min)
sudo apt install -y certbot python3-certbot-nginx
sudo certbot certonly --nginx -d domains.unnar.id

# 6. Queue Workers (5 min)
sudo apt install -y supervisor
sudo cp config/supervisor-queue.conf /etc/supervisor/conf.d/unnar-domains-queue.conf
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start unnar-domains-queue:*

# Total: ~32 minutes
```

---

## 📋 Essential Environment Variables

```env
# Required (must be configured)
APP_KEY=base64:YOUR_KEY_FROM_artisan_key_generate
DB_PASSWORD=your_database_password
RADNET_API_KEY=your_api_key
RADNET_API_SECRET=your_api_secret
MAIL_PASSWORD=your_smtp_password

# Recommended to change
DB_HOST=localhost
DB_DATABASE=unnar_domains
DB_USERNAME=unnar_app
MAIL_FROM_ADDRESS=noreply@unnar.id
```

---

## 🔍 Post-Deployment Verification

```bash
# 1. Check application loads
curl -I https://domains.unnar.id
# Expected: 200 OK with Laravel headers

# 2. Check web server status
sudo systemctl status nginx   # or apache2

# 3. Check queue workers
sudo supervisorctl status unnar-domains-queue:*
# Expected: all processes RUNNING

# 4. Check database connection
php artisan tinker
> DB::connection()->getPdo()
# Expected: PDOConnection object

# 5. View application logs
tail -f /var/www/unnar-domains/storage/logs/laravel.log

# 6. Check SSL certificate
curl -v https://domains.unnar.id
# Expected: ssl/TLS handshake successful
```

---

## 🆘 Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| **Port 8000 not accessible** | Check firewall: `sudo ufw status` |
| **Database connection error** | Verify DB_HOST, DB_USERNAME, DB_PASSWORD in .env |
| **Queue not processing** | Check supervisor: `sudo supervisorctl status` |
| **SSL not working** | Run certbot again: `sudo certbot renew` |
| **Assets not loading** | Rebuild: `npm run build` then clear cache |
| **Permission denied errors** | Fix ownership: `sudo chown -R www-data:www-data /var/www/unnar-domains` |
| **Out of memory** | Increase PHP memory_limit in php.ini or supervisor command |

---

## 📊 Monitoring Commands

```bash
# System resources
top
htop
df -h  # disk space
free -h  # memory

# Web server logs
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log

# Application logs
tail -f /var/www/unnar-domains/storage/logs/laravel.log

# Queue logs
tail -f /var/www/unnar-domains/storage/logs/queue.log

# Database status
mysql -u unnar_app -p unnar_domains -e "SHOW PROCESSLIST;"

# Supervisor status
sudo supervisorctl status
sudo supervisorctl tail unnar-domains-queue:* -f

# Queue depth
php artisan queue:failed
php artisan queue:monitor
```

---

## 🔐 Security Checklist

- [ ] `.env` file not in version control
- [ ] APP_DEBUG=false in production
- [ ] SSL certificate installed (check expiration monthly)
- [ ] Database user has minimal required permissions
- [ ] SSH key-based authentication only (no passwords)
- [ ] Firewall configured (only 22, 80, 443 ports)
- [ ] Fail2Ban installed and configured
- [ ] Regular backups automated
- [ ] Log rotation configured
- [ ] Monitoring/alerting enabled

---

## 🔄 Maintenance Schedule

### Daily (automated or manual check)
```bash
# Check for errors
grep ERROR /var/www/unnar-domains/storage/logs/laravel.log

# Check disk space
df -h
```

### Weekly
```bash
# Review activity
tail -100 /var/www/unnar-domains/storage/logs/laravel.log

# Check SSL expiration (30+ days remaining)
echo | openssl s_client -servername domains.unnar.id -connect domains.unnar.id:443 2>/dev/null | openssl x509 -noout -dates

# Verify backups
ls -lh /var/backups/unnar-domains/
```

### Monthly
```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Restart services to apply updates
sudo systemctl restart php8.3-fpm nginx
sudo supervisorctl restart all

# Review logs for issues
sudo systemctl status nginx
sudo systemctl status mysql
```

### Quarterly
```bash
# Security audit
sudo fail2ban-client status

# Database maintenance
mysql> OPTIMIZE TABLE domains;
mysql> ANALYZE TABLE activity_logs;

# Clean old logs
find /var/www/unnar-domains/storage/logs -mtime +30 -delete
```

---

## 🆘 Emergency Procedures

### If queue is stuck:
```bash
sudo supervisorctl restart unnar-domains-queue:*
# Or clear specific failed jobs:
php artisan queue:failed
php artisan queue:retry <id>
```

### If database is locked:
```bash
# Restart MySQL
sudo systemctl restart mysql

# Or check what's holding the lock
mysql> SHOW PROCESSLIST;
mysql> KILL <process_id>;
```

### If disk is full:
```bash
df -h  # Check space
du -sh *  # Check directory sizes
# Clear old logs:
rm /var/www/unnar-domains/storage/logs/laravel.log.*
# Clear cache:
php artisan cache:clear
```

### If application won't start:
```bash
# Check error logs
tail -50 /var/www/unnar-domains/storage/logs/laravel.log
tail -50 /var/log/php8.3-fpm.log

# Verify configuration
php artisan config:cache --env=production
```

---

## 📞 Important Contacts & URLs

- **Laravel Docs:** https://laravel.com/docs
- **Nginx Docs:** https://nginx.org/en/docs/
- **MySQL Docs:** https://dev.mysql.com/doc/
- **SSL Labs:** https://www.ssllabs.com/ssltest/
- **Let's Encrypt:** https://letsencrypt.org/
- **Status Check:** https://domains.unnar.id/

---

## 📝 Important Files

| File | Purpose | Location |
|------|---------|----------|
| `.env.production` | Production environment config | Root directory |
| `nginx-production.conf` | Web server config | `config/nginx-production.conf` |
| `supervisor-queue.conf` | Queue worker config | `config/supervisor-queue.conf` |
| `setup-production-db.sh` | Database setup script | `scripts/setup-production-db.sh` |
| `DEPLOYMENT_GUIDE.md` | Full deployment docs | Root directory |
| `SECURITY_HARDENING.md` | Security best practices | Root directory |

---

## 💾 Backup Commands

```bash
# Manual database backup
mysqldump -u unnar_app -p unnar_domains > backup_$(date +%Y%m%d).sql

# Restore from backup
mysql -u unnar_app -p unnar_domains < backup_20260212.sql

# Full application backup
tar -czf app_backup_$(date +%Y%m%d).tar.gz /var/www/unnar-domains

# List backups
ls -lh /var/backups/unnar-domains/
```

---

## 🎯 Performance Optimization

```bash
# Clear all caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dumpautoload -o

# Database optimization
php artisan optimize

# Increase PHP limits (if needed)
# Edit /etc/php/8.3/fpm/php.ini
memory_limit=512M
max_execution_time=300
upload_max_filesize=100M
```

---

## ✅ Success Indicators

- ✅ `curl https://domains.unnar.id` returns 200 OK
- ✅ `curl https://domains.unnar.id/login` loads login page
- ✅ `sudo supervisorctl status` shows all queue workers RUNNING
- ✅ `php artisan tinker` connects to database successfully
- ✅ No ERROR entries in recent logs
- ✅ SSL Labs score A+ (preferred) or A (minimum)
- ✅ Database connection established
- ✅ Queue jobs processing (check failed jobs: `php artisan queue:failed`)

---

**Last Updated:** February 12, 2026  
**Version:** 1.0  
**Status:** Production Ready ✅

