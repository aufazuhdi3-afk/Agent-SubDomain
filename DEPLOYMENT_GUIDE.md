# Production Deployment Guide - Unnar Domain Service

## Pre-Deployment Checklist

### Environment Preparation
- [ ] Production server ready (Ubuntu 20.04+ or similar)
- [ ] PHP 8.2+ with FPM installed
- [ ] MySQL 8.0+ or equivalent database server
- [ ] Redis (optional but recommended for queue/cache)
- [ ] Nginx or Apache 2.4+
- [ ] Git installed on server
- [ ] SSL certificate (Let's Encrypt)
- [ ] Domain DNS configured

### Application Setup
- [ ] Code cloned from repository
- [ ] Composer installed
- [ ] Node.js/npm installed for asset compilation
- [ ] .env.production configured with production values
- [ ] Database created and migrations run
- [ ] Assets built (npm run build)

### Security Configuration
- [ ] SSL certificate installed and configured
- [ ] Firewall configured (only 80, 443, 22 ports exposed)
- [ ] SSH keys configured (no password login)
- [ ] Fail2ban or ModSecurity installed
- [ ] Application secrets set (APP_KEY, RADNET_API_KEY, etc.)
- [ ] Database password changed from default
- [ ] Mail credentials configured
- [ ] CORS configured to only accept unnar.id

### Monitoring Setup
- [ ] Log rotation configured
- [ ] Application monitoring tool (Sentry, etc.)
- [ ] Database backup automated (daily)
- [ ] Disk space monitoring
- [ ] Memory monitoring
- [ ] CPU monitoring

---

## Step-by-Step Deployment

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 with extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-redis \
  php8.3-curl php8.3-gd php8.3-mbstring php8.3-zip php8.3-xml

# Install MySQL
sudo apt install -y mysql-server

# Install Redis (optional)
sudo apt install -y redis-server

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Application Deployment

```bash
# Create application directory
sudo mkdir -p /var/www/unnar-domains
cd /var/www/unnar-domains

# Clone repository (using deploy key or SSH)
sudo git clone git@github.com:yourorg/unnar-domains.git .

# Set permissions
sudo chown -R www-data:www-data /var/www/unnar-domains
sudo chmod -R 755 /var/www/unnar-domains
sudo chmod -R 775 storage bootstrap/cache

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Compile assets
sudo -u www-data npm install
sudo -u www-data npm run build

# Copy production environment
sudo -u www-data cp .env.production .env

# Generate application key (if not already in .env)
sudo -u www-data php artisan key:generate --env=production

# Set up database
sudo php scripts/setup-production-db.sh

# Run migrations
sudo -u www-data php artisan migrate --env=production --force

# Seed admin user (optional)
sudo -u www-data php artisan db:seed --env=production

# Clear caches
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 3. Web Server Configuration

#### For Nginx:
```bash
# Copy Nginx configuration
sudo cp config/nginx-production.conf /etc/nginx/sites-available/unnar-domains

# Enable site
sudo ln -s /etc/nginx/sites-available/unnar-domains /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx

# Enable PHP-FPM
sudo systemctl enable php8.3-fpm
sudo systemctl start php8.3-fpm
```

#### For Apache:
```bash
# Copy Apache configuration
sudo cp config/apache-production.conf /etc/apache2/sites-available/unnar-domains.conf

# Enable site
sudo a2ensite unnar-domains

# Enable required modules
sudo a2enmod rewrite ssl http2

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

### 4. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --nginx -d domains.unnar.id

# Auto-renewal (already enabled by default)
sudo systemctl enable certbot.timer
```

### 5. Queue Worker Setup

```bash
# Install Supervisor
sudo apt install -y supervisor

# Copy supervisor configuration
sudo cp config/supervisor-queue.conf /etc/supervisor/conf.d/unnar-domains-queue.conf

# Enable supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor

# Start queue workers
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start unnar-domains-queue:*

# Check status
sudo supervisorctl status unnar-domains-queue:*
```

### 6. Cron Jobs

```bash
# Edit crontab
sudo -u www-data crontab -e

# Add the following line to run Laravel scheduler every minute:
* * * * * php /var/www/unnar-domains/artisan schedule:run >> /dev/null 2>&1
```

### 7. Database Backup Setup

```bash
# Create backup directory
sudo mkdir -p /var/backups/unnar-domains
sudo chown www-data:www-data /var/backups/unnar-domains

# Create backup script
cat > /usr/local/bin/backup-unnar-domains.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/unnar-domains"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="unnar_domains"
DB_USER="unnar_reader"

mysqldump -u"$DB_USER" -p"$READONLY_PASSWORD" "$DB_NAME" | gzip > "$BACKUP_DIR/db_$TIMESTAMP.sql.gz"
tar -czf "$BACKUP_DIR/app_$TIMESTAMP.tar.gz" -C /var/www unnar-domains/storage --exclude=framework/cache

# Keep only last 7 days of backups
find "$BACKUP_DIR" -type f -mtime +7 -delete
EOF

sudo chmod +x /usr/local/bin/backup-unnar-domains.sh

# Add to crontab to run daily at 2 AM
sudo -u www-data crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-unnar-domains.sh
```

### 8. Monitoring & Logging

```bash
# Install Sentry (optional, for error tracking)
# https://sentry.io/onboarding/

# Configure log rotation
sudo tee /etc/logrotate.d/unnar-domains > /dev/null <<EOF
/var/www/unnar-domains/storage/logs/*.log {
    daily
    rotate 14
    missingok
    notifempty
    compress
    delaycompress
    postrotate
        systemctl reload php8.3-fpm > /dev/null 2>&1 || true
    endscript
}
EOF

# Install and configure monitoring
sudo apt install -y htop iotop nethogs
```

---

## Post-Deployment Verification

```bash
# Test application access
curl -I https://domains.unnar.id

# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Check Nginx status
sudo systemctl status nginx

# Check queue worker
sudo supervisorctl status unnar-domains-queue:*

# Check database connection
sudo -u www-data php artisan tinker
> \DB::connection()->getPdo()

# View logs
tail -f /var/www/unnar-domains/storage/logs/laravel.log
```

---

## Troubleshooting

### Application not loading
```bash
# Check PHP-FPM logs
sudo tail -f /var/log/php8.3-fpm.log

# Check web server logs
sudo tail -f /var/log/nginx/error.log  # Nginx
sudo tail -f /var/log/apache2/error.log  # Apache

# Check application logs
tail -f /var/www/unnar-domains/storage/logs/laravel.log

# Clear caches
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
```

### Queue not processing
```bash
# Check queue worker status
sudo supervisorctl status unnar-domains-queue:*

# Restart queue workers
sudo supervisorctl restart unnar-domains-queue:*

# Check queue logs
tail -f /var/www/unnar-domains/storage/logs/queue.log
```

### Database connection issues
```bash
# Test MySQL connection
mysql -u unnar_app -p -h localhost unnar_domains

# Check MySQL logs
sudo tail -f /var/log/mysql/error.log
```

### Performance issues
```bash
# Monitor system resources
top
htop
iotop

# Check Redis (if used)
redis-cli ping
redis-cli dbsize

# Check database performance
mysql> SHOW PROCESSLIST;
```

---

## Security Hardening

### 1. Firewall Configuration
```bash
sudo ufw enable
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### 2. Fail2Ban
```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. ModSecurity (Apache only)
```bash
sudo apt install -y libapache2-mod-security2
```

### 4. SSH Hardening
```bash
sudo nano /etc/ssh/sshd_config
# Change: PermitRootLogin no
# Change: PasswordAuthentication no
# Change: Port 22 (to different port if desired)

sudo systemctl restart ssh
```

---

## Production Environment Variables

Update `.env.production` with:
```
APP_KEY=<run php artisan key:generate>
RADNET_API_KEY=<your_api_key>
RADNET_API_SECRET=<your_api_secret>
MAIL_HOST=<your_smtp_host>
MAIL_USERNAME=<your_email>
MAIL_PASSWORD=<your_password>
DB_PASSWORD=<your_db_password>
```

---

## Rollback Procedure

```bash
# If deployment fails, rollback to previous version
cd /var/www/unnar-domains

# Check git log
git log --oneline

# Rollback to previous commit
sudo -u www-data git checkout <previous_commit>

# Reinstall dependencies if needed
sudo -u www-data composer install

# Clear caches
sudo -u www-data php artisan cache:clear

# Restart application
sudo systemctl restart php8.3-fpm
```

---

## Monitoring Commands

```bash
# View application status
sudo supervisorctl status

# Monitor queue depth
sudo -u www-data php artisan queue:failed

# Check active jobs
sudo -u www-data php artisan queue:monitor

# View application errors
tail -f /var/www/unnar-domains/storage/logs/laravel.log

# Monitor system
dmesg -w  # kernel logs
journalctl -f  # system logs
```

---

## Regular Maintenance

### Daily
- [ ] Monitor error logs
- [ ] Check disk space
- [ ] Check queue processing

### Weekly
- [ ] Review user activity logs
- [ ] Check domain provisioning status
- [ ] Verify backups completed

### Monthly
- [ ] Update system packages
- [ ] Review security settings
- [ ] Clean up old logs
- [ ] Verify SSL certificate expiration

### Quarterly
- [ ] Security audit
- [ ] Performance optimization
- [ ] Disaster recovery test

---

## Support & Documentation

- Laravel Docs: https://laravel.com/docs
- Nginx Docs: https://nginx.org/en/docs/
- MySQL Docs: https://dev.mysql.com/doc/
- Supervisor Docs: http://supervisord.org/
- Let's Encrypt: https://letsencrypt.org/

