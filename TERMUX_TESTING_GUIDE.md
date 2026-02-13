# Unnar Domain Service - Termux Testing Guide

**Panduan Testing Aplikasi Laravel di HP menggunakan Termux**

---

## 📱 Prerequisites

### Install Termux
1. Download Termux dari [F-Droid](https://f-droid.org/en/packages/com.termux/) atau [GitHub Releases](https://github.com/termux/termux-app/releases)
   - **JANGAN** dari Google Play Store (outdated)
   - Gunakan F-Droid untuk versi terbaru

2. Berikan permission Storage (penting untuk clone repo):
   ```bash
   termux-setup-storage
   ```

### Spesifikasi HP yang Disarankan
- **RAM:** Minimal 3GB (recommended 4GB+)
- **Storage:** 2GB untuk aplikasi + dependencies
- **OS:** Android 8.0+ (recommended 10+)
- **Processor:** Snapdragon 660 atau lebih baik

---

## 🚀 Step 1: Setup Termux Environment

### A. Update Paket Manager
```bash
# Perbarui package manager
pkg update && pkg upgrade -y

# Ini akan memakan waktu ~10-15 menit jika first time
```

### B. Install Dasar Tools
```bash
# Install git, curl, wget
pkg install -y git curl wget

# Install text editors
pkg install -y nano vim
```

---

## 💻 Step 2: Install PHP & Composer

### A. Install PHP 8.3
```bash
# Install PHP dengan extensions yang diperlukan
pkg install -y php php-mysql php-gd php-curl php-xml php-zip php-fpm

# Verifikasi instalasi
php -v
# Expected: PHP 8.3.x
```

### B. Install Composer
```bash
# Download Composer installer
curl -sS https://getcomposer.org/installer -o composer-setup.php

# Install Composer
php composer-setup.php --install-dir=/data/data/com.termux/files/usr/bin --filename=composer

# Verifikasi
composer --version
```

### C. Install Node.js & npm
```bash
# Install Node.js
pkg install -y nodejs npm

# Verifikasi
node --version
npm --version
```

---

## 📥 Step 3: Clone Repository

### A. Navigasi ke Storage
```bash
# Masuk ke storage eksternal
cd ~/storage/downloads

# Atau gunakan direktori berbeda
mkdir -p ~/Code
cd ~/Code
```

### B. Clone Repository
```bash
# Clone dari GitHub
git clone https://github.com/aufazuhdi3-afk/Agent-SubDomain.git

# Masuk direktori
cd Agent-SubDomain
```

### C. Verifikasi Clone
```bash
# Cek struktur project
ls -la

# Expected output: app/, routes/, database/, composer.json, package.json, dll
```

---

## ⚙️ Step 4: Install Dependencies

### A. Install Composer Dependencies
```bash
# Install PHP dependencies (akan lebih lambat di HP)
composer install

# Jika ingin development version (dengan dev tools):
# composer install --no-interaction

# Proses ini bisa memakan 15-30 menit tergantung kecepatan HP
```

### B. Install npm Dependencies
```bash
# Install frontend dependencies
npm install

# Ini juga akan memakan 10-20 menit

# Build assets
npm run build

# Result: public/build/ folder dengan compiled assets
```

### C. Troubleshooting Dependencies

**Jika memory error:**
```bash
# Increase Node memory
export NODE_OPTIONS="--max-old-space-size=512"

# Retry npm install
npm install
```

**Jika storage penuh:**
```bash
# Bersihkan npm cache
npm cache clean --force

# Atau gunakan ci untuk install yang lebih cepat
npm ci --prefer-offline
```

---

## 🔧 Step 5: Setup Environment

### A. Create .env File
```bash
# Copy environment file
cp .env.example .env

# Edit sesuai Termux environment
nano .env
```

### B. Configure .env for Termux
```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://192.168.x.x:8000

# Database (gunakan SQLite di Termux)
DB_CONNECTION=sqlite
# DB_DATABASE=/path/to/database.sqlite

# Cache & Session
CACHE_STORE=file
SESSION_DRIVER=file

# Queue
QUEUE_CONNECTION=sync  # Gunakan sync untuk testing (tidak memerlukan worker)

# Mail
MAIL_MAILER=log
```

### C. Generate Application Key
```bash
# Generate APP_KEY
php artisan key:generate

# Verifikasi
grep APP_KEY .env
```

### D. Setup Database
```bash
# Buat database file (jika tidak ada)
touch database/database.sqlite

# Jalankan migrations
php artisan migrate --seed

# Expected output: Tables created successfully
```

---

## 🚀 Step 6: Run Application

### OPTION 1: Simple Server (Termux Native)

**Terminal 1 - Run PHP Server**
```bash
# Mulai PHP development server
php artisan serve --host=0.0.0.0 --port=8000

# Output:
# Laravel development server started:
# http://0.0.0.0:8000
```

**Terminal 2 (buka terminal baru) - Test Aplikasi**
```bash
# Dapatkan IP HP
ifconfig wlan0
# Lihat inet addr, contoh: 192.168.1.100

# Test dari Termux
curl http://localhost:8000

# Atau access dari browser di HP:
# http://localhost:8000
# atau http://192.168.1.100:8000 (dari device lain)
```

### OPTION 2: Dengan Ngrok (Akses dari Device Lain)

**Step 1: Install Ngrok di Termux**
```bash
# Download ngrok
wget https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-arm64.zip

# Extract
unzip ngrok-stable-linux-arm64.zip

# Move ke /usr/bin
mv ngrok /data/data/com.termux/files/usr/bin
```

**Step 2: Setup Ngrok Auth (Optional)**
```bash
# Sign up di https://ngrok.com
# Then authenticate
ngrok config add-authtoken YOUR_TOKEN
```

**Step 3: Buka Port dengan Ngrok**
```bash
# Terminal 1: Laravel server
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2: Buka Ngrok tunnel
ngrok http 8000

# Output akan menunjukkan public URL:
# https://xxxxx-xx-xxx-xxx.ngrok.io → http://localhost:8000
```

---

## 📱 Step 7: Access dari Browser HP

### Local Access
1. Buka browser di HP
2. Masuk: `http://localhost:8000`
3. Atau: `http://192.168.1.x:8000` (ganti IP)

### Login Credentials
```
Email:    admin@unnar.id
Password: password
```

### Fitur untuk Di-Test
- [ ] Homepage loading
- [ ] Login functionality
- [ ] Dashboard display
- [ ] Create domain request form
- [ ] Admin domain list (login as admin)
- [ ] Domain approval button
- [ ] Activity logs

---

## 🔍 Monitoring & Debugging

### View Saat Running
```bash
# Terminal baru - follow logs real-time
tail -f storage/logs/laravel.log

# Atau use artisan tail command
php artisan tinker

# Inside tinker:
> \DB::connection()->getPdo()  // Test database
> \App\Models\User::count()     // Test models
```

### Database Inspection
```bash
# Buka SQLite dari Termux
sqlite3 database/database.sqlite

# SQL commands:
> .tables
> SELECT * FROM users;
> SELECT * FROM domains;
> .quit
```

### Check Storage Usage
```bash
# Lihat ukuran folder
du -sh .

# Cek disk space
df -h

# Bersihkan cache jika perlu
php artisan cache:clear
rm -rf bootstrap/cache/*
```

---

## ⚡ Performance Tips untuk Termux

### 1. Disable Debug Mode untuk Testing Lebih Cepat
```env
APP_DEBUG=false
```

### 2. Use Command Caching
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### 3. Optimize Autoloader
```bash
composer dumpautoload -o
```

### 4. Reduce npm Disk Usage
```bash
# Keep only production dependencies
npm ci --only=production
```

---

## 🔧 Troubleshooting

### Error: "php not found"
```bash
# Reinstall PHP
pkg install -y php

# Add to PATH if needed
echo "export PATH=/data/data/com.termux/files/usr/bin:$PATH" >> ~/.bashrc
source ~/.bashrc
```

### Error: "Composer install timeout"
```bash
# Increase timeout
composer install --no-interaction --no-ansi

# Atau specify process timeout
composer install --no-interaction --process-timeout=600
```

### Error: "npm ERR! out of memory"
```bash
# Increase Node memory
export NODE_OPTIONS="--max-old-space-size=1024"

# Retry
npm install
```

### Error: "SQLite database locked"
```bash
# Remove old database
rm database/database.sqlite

# Create new one
touch database/database.sqlite

# Run migrations fresh
php artisan migrate --seed --fresh
```

### Aplikasi Lambat/Freeze
```bash
# Restart Termux
# Tutup semua terminal dan buka ulang

# Clear old processes
fg

# Kill all php processes if stuck
pkill -f php
```

---

## 📊 Expected Performance

| Task | Duration | Notes |
|------|----------|-------|
| Package Update | 10-15 min | First time only |
| Composer Install | 15-30 min | Depends on dependencies & HP speed |
| npm install | 10-20 min | Depends on packages & network |
| npm build | 5-10 min | First time |
| Database migration | 30 sec | Depends on schema size |
| Home page load | 2-5 sec | Normal response time |

---

## 🔐 Testing Login Workflow

### Test Admin Panel
```
1. Go to: http://localhost:8000/login
2. Email: admin@unnar.id
3. Password: password
4. Click Login
5. Should redirect to: http://localhost:8000/dashboard
6. Access admin panel: http://localhost:8000/admin/domains
```

### Test User Domain Request
```
1. Login as admin
2. Go to: http://localhost:8000/domains
3. Click "Create Domain Request"
4. Fill form:
   - Subdomain: test-domain-001
   - Target IP: 192.168.1.100
5. Click Submit
6. Should appear in domain list with "pending" status
```

### Test Admin Approval
```
1. Go to: http://localhost:8000/admin/domains
2. Find new domain request
3. Click "Approve" button
4. Domain status should change to "approved"
```

---

## 📚 Testing Mode Features

### Development Features Enabled
✅ Debug mode (set APP_DEBUG=true in .env)  
✅ Frontend debugging tools  
✅ Database query logs  
✅ Error stack traces  

### Testing Without Queue
```bash
# In .env, set:
QUEUE_CONNECTION=sync

# This makes provisioning instant (for testing)
# No need to start queue workers
```

### Test Email (Log Driver)
```bash
# Emails sent to log file instead of SMTP
# Check: storage/logs/laravel.log
```

---

## 🎯 Quick Start Commands (Copy-Paste)

```bash
# 1. Setup Termux (run once)
pkg update && pkg upgrade -y
pkg install -y git curl wget php php-mysql nodejs npm
curl -sS https://getcomposer.org/installer | php -- --install-dir=/data/data/com.termux/files/usr/bin --filename=composer

# 2. Clone & Setup (run once)
cd ~/Code
git clone https://github.com/aufazuhdi3-afk/Agent-SubDomain.git
cd Agent-SubDomain
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed

# 3. Run Application (every time)
php artisan serve --host=0.0.0.0 --port=8000

# 4. Access
# Browser: http://localhost:8000
# Login: admin@unnar.id / password
```

---

## 📞 Support Tips

**If stuck:**
1. Check error logs: `tail -f storage/logs/laravel.log`
2. Test database: `php artisan tinker` → `\DB::connection()->getPdo()`
3. Test setup: `php artisan list` (should show all commands)
4. Restart: Kill Termux and reopen (full reset)

**For better experience:**
- Use WiFi (faster than mobile data)
- Have at least 4GB RAM on HP
- Close other apps while running
- Use Chromium/Firefox browser (lighter than Chrome)

---

## ✅ Success Indicators

You know it's working when:
- ✅ `php artisan serve` shows "Server running"
- ✅ Home page loads in browser
- ✅ Login page accessible
- ✅ Can login with admin@unnar.id
- ✅ Dashboard displays
- ✅ No red error messages
- ✅ Database operations respond quickly

---

**Date Updated:** February 13, 2026  
**Status:** Ready for Termux Testing ✅

