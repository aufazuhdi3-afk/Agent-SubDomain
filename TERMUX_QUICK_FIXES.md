# Termux Testing - Quick Fixes

**Solusi cepat untuk 2 error yang sering di-encounter di Termux**

---

## ⚠️ Error 1: Vite Manifest Not Found

**Error:** `Illuminate\Foundation\ViteManifestNotFoundException - Vite manifest not found at: /data/data/com.termux/files/home/Agent-SubDomain/public/build/manifest.json`

### Fix (Choose 1):

#### **Option A: Rebuild Assets (RECOMMENDED)**
```bash
cd ~/Code/Agent-SubDomain

# Clean build
rm -rf node_modules package-lock.json
npm install
npm run build

# Check jika build berhasil
ls -la public/build/manifest.json
```

#### **Option B: Skip Vite (Quick Workaround)**

Edit `.env`:
```env
VITE_DEV_SERVER_URL=http://localhost:5173
VITE=false
```

Or set inline:
```bash
VITE_DEV_URL=http://localhost:5173 php artisan serve --host=0.0.0.0 --port=8000
```

#### **Option C: Use Public Assets (Fallback)**

1. Remove `@vite()` dari views
2. Update file: `resources/views/layouts/guest.blade.php` line 15
3. Replace dengan:
```blade
<!-- Fallback CSS -->
<link rel="stylesheet" href="https://cdn.tailwindcss.com">
```

---

## ⚠️ Error 2: Call to undefined method middleware()

**Error:** `Call to undefined method App\Http\Controllers\AdminDomainController::middleware()`

### ✅ Solution (FIXED in Latest)

**File sudah di-fix:** `app/Http/Controllers/Controller.php`

Sekarang extends dari Laravel's BaseController dengan middleware support.

**Jika masih error setelah pull:**
```bash
cd ~/Code/Agent-SubDomain
git pull origin main
php artisan cache:clear
php artisan config:clear
```

---

## 🔧 Complete Termux Fix Workflow

```bash
# 1. Pull latest code (sudah fix controllers)
cd ~/Code/Agent-SubDomain
git pull origin main

# 2. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 3. Rebuild dependencies
composer install
npm install
npm run build

# 4. Setup database jika belum
touch database/database.sqlite
php artisan migrate --seed --fresh

# 5. Run server
php artisan serve --host=0.0.0.0 --port=8000
```

---

## ⚡ Quick Workaround untuk Termux (Kalau mau Cepat)

Jika npm build timeout atau masalah:

```bash
# Edit .env untuk skip Vite compilation
echo "VITE_DEV_URL=false" >> .env

# OR manually set in .env:
# Tambahin line: APP_ENV=production

# Maka Vite akan autoconfigure fallback

php artisan serve --host=0.0.0.0 --port=8000
```

---

## 📋 Checking Status

```bash
# 1. Check Vite manifest exists
ls -la public/build/manifest.json
echo "Manifest status: $?"  # 0 = exists

# 2. Check Controller extends properly
grep -n "extends BaseController" app/Http/Controllers/Controller.php

# 3. Test database
php artisan tinker
> DB::connection()->getPdo()
> User::count()
> exit

# 4. Test routes
php artisan route:list | grep admin
```

---

## 🎯 Expected Success

Setelah fixes:
```
✓ Homepage loads tanpa error
✓ Login page display dengan styling
✓ Login berhasil
✓ Dashboard accessible
✓ Admin panel accessible
✓ No Vite errors
✓ No middleware errors
```

---

## 💾 Commands Summary

```bash
# Full reset jika masih error
cd ~/Code/Agent-SubDomain
git pull origin main
rm -rf database/database.sqlite
rm -rf storage/logs/*
php artisan config:clear && php artisan cache:clear
composer install
npm install && npm run build
touch database/database.sqlite
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8000
```

---

**Status:** ✅ Fixes Applied  
**Date:** February 13, 2026

