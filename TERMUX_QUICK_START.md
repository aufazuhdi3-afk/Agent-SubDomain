# Quick Reference - Testing di Termux (Android)

**Ringkasan Cepat untuk Testing Aplikasi di HP**

---

## 📱 HANYA 5 LANGKAH UTAMA

### **LANGKAH 1: Install Termux (5 menit)**
```
1. Download dari F-Droid (bukan Play Store!)
2. Buka Termux
3. Run: termux-setup-storage
4. Berikan permission storage
```

### **LANGKAH 2: Setup Environment (20 menit)**
```bash
pkg update && pkg upgrade -y
pkg install -y git php php-mysql nodejs npm
curl -sS https://getcomposer.org/installer | php -- --install-dir=/data/data/com.termux/files/usr/bin --filename=composer
```

### **LANGKAH 3: Clone Repository (< 5 menit)**
```bash
mkdir ~/Code
cd ~/Code
git clone https://github.com/aufazuhdi3-afk/Agent-SubDomain.git
cd Agent-SubDomain
```

### **LANGKAH 4: Install Dependencies (30-45 menit)**
```bash
composer install
npm install && npm run build
```

### **LANGKAH 5: Setup & Run (5 menit)**
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 🌐 AKSES APLIKASI

### Di HP Termux
```
Browser: http://localhost:8000
```

### Dari HP Lain / Komputer (Via WiFi)
```bash
# 1. Cari IP HP
ifconfig wlan0
# Output: inet addr: 192.168.1.100 (contoh)

# 2. Access dari device lain:
http://192.168.1.100:8000
```

### Via Internet (Ngrok)
```bash
# Terminal 1: Laravel server
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2: Create tunnel
ngrok http 8000

# Get public URL dari ngrok output
https://xxx-xxx-xxx-xxx.ngrok.io
```

---

## 🔐 LOGIN CREDENTIALS

```
Email:    admin@unnar.id
Password: password
```

---

## 🎯 TESTING CHECKLIST

- [ ] Homepage loads (`http://localhost:8000`)
- [ ] Login page accessible (`/login`)
- [ ] Can login dengan admin@unnar.id
- [ ] Dashboard displays dengan statistics
- [ ] Create Domain form accessible (`/domains/create`)
- [ ] Can submit domain request
- [ ] Admin panel shows pending domains (`/admin/domains`)
- [ ] Can approve domain request
- [ ] Status changes to "approved"
- [ ] No red error messages
- [ ] Database operations responsive

---

## ⚡ PERFORMANCE EXPECTATIONS

| Operation | Time | Notes |
|-----------|------|-------|
| Setup (packages) | ~20 min | One-time only |
| Composer install | 15-30 min | Depends on HP speed |
| npm install | 10-20 min | Depends on network |
| npm build | 5-10 min | Asset compilation |
| App startup | 1-2 sec | Normal speed |
| Page load | 2-5 sec | Normal response |
| Database query | <1 sec | SQLite on mobile |

---

## 🔧 TROUBLESHOOTING QUICK FIXES

### "Permission denied" atau "command not found"
```bash
# Fix PATH
echo "export PATH=/data/data/com.termux/files/usr/bin:$PATH" >> ~/.bashrc
source ~/.bashrc
```

### "Out of memory" di npm
```bash
export NODE_OPTIONS="--max-old-space-size=512"
npm install  # retry
```

### "Database locked" error
```bash
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate --seed --fresh
```

### Aplikasi tidak loading
```bash
# Cek logs
tail -f storage/logs/laravel.log

# Test database
php artisan tinker
> \DB::connection()->getPdo()
> exit
```

### Termux freeze
```bash
# Tekan Ctrl+C untuk stop
# Close dan reopen Termux untuk reset
```

---

## 📚 FULL GUIDE

Untuk detail lebih lengkap, baca: **TERMUX_TESTING_GUIDE.md**

Di repository:
https://github.com/aufazuhdi3-afk/Agent-SubDomain/blob/main/TERMUX_TESTING_GUIDE.md

---

## 💾 SAVE TIME: Copy-Paste Commands

**Setup One-Time (Run Once):**
```bash
pkg update && pkg upgrade -y && pkg install -y git curl wget php php-mysql nodejs npm && curl -sS https://getcomposer.org/installer | php -- --install-dir=/data/data/com.termux/files/usr/bin --filename=composer && mkdir -p ~/Code && cd ~/Code && git clone https://github.com/aufazuhdi3-afk/Agent-SubDomain.git && cd Agent-SubDomain && cp .env.example .env && composer install && npm install && npm run build && php artisan key:generate && touch database/database.sqlite && php artisan migrate --seed
```

**Run Application (Every Time):**
```bash
cd ~/Code/Agent-SubDomain
php artisan serve --host=0.0.0.0 --port=8000
```

---

## ✅ EXPECTED SUCCESS

Ketika berhasil, Anda akan melihat:
```
   INFO  Server running on [http://0.0.0.0:8000].

  Press Ctrl+C to quit.
```

Lalu buka browser:
- HP sendiri: `http://localhost:8000`
- HP lain: `http://[IP_HP]:8000`
- Internet: `https://[ngrok-url]`

---

**Status:** Ready to Test ✅  
**Date:** February 13, 2026  

