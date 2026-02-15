# ⚡ Quick Fix: Termux Database Error

## 🎯 The Problem
```
SQLSTATE[HY000]: General error: 1 no such column: subdomain_limit
```

Error terjadi saat mencoba update user di `/admin/users/2` karena kolom `subdomain_limit` belum ada di database Termux Anda.

---

## ✅ Solusi Cepat (Di Termux)

### Method 1: Auto-Fix Script (Recommended)
```bash
# Copy file fix script dari workspace ke Termux, lalu:
cd ~/Agent-SubDomain
bash fix-subdomain-limit.sh
```

**Apa yang dilakukan script:**
- ✅ Backup database lama
- ✅ Run migration untuk add column
- ✅ Verify column exists
- ✅ Show data confirmation

---

### Method 2: Manual Migration (Jika script tidak work)
```bash
cd ~/Agent-SubDomain

# Run migration
php artisan migrate

# Verify
sqlite3 database/database.sqlite "SELECT id, name, email, subdomain_limit FROM users;"
```

---

### Method 3: Database Reset (Jika stuck)
```bash
cd ~/Agent-SubDomain

# Backup
cp database/database.sqlite database/database.sqlite.bak

# Fresh
rm database/database.sqlite
php artisan migrate
php artisan db:seed

# Login dengan: admin@unnar.id / password
```

---

## ✨ After Fix

1. **Refresh browser** - Jangan lupa clear cache (Ctrl+Shift+Delete)
2. **Go to**: `http://localhost/admin/users`
3. **Edit user** - Should work now! ✅
4. **Update subdomain limit** - Set 3, 5, unlimited, etc.

---

## 📝 Verification Commands

```bash
# Check if column exists
sqlite3 database/database.sqlite "PRAGMA table_info(users);" | grep subdomain_limit

# See all users with limits
sqlite3 database/database.sqlite "SELECT id, name, role, subdomain_limit FROM users;"

# See migration status
php artisan migrate:status
```

---

## 🆘 If Still Error After Fix

Try:
```bash
# Clear cached config
php artisan config:cache

# Restart server
pkill -f "php artisan serve"

# Start fresh
php artisan serve
```

---

## 📚 Why This Happened

1. ✅ Migration file created: `database/migrations/2026_02_15_185419_add_subdomain_limit_to_users_table.php`
2. ✅ Pushed to GitHub
3. ⚠️ **But**, tidak di-run di Termux database (created before migration push)

---

## 🚀 Status After Fix

- ✅ Admin bisa edit user tanpa error
- ✅ Admin bisa set/change subdomain limits
- ✅ User dashboard menampilkan quota dengan benar
- ✅ Domain creation akan check limit per-user

---

**Created**: 2026-02-15
**For**: Termux environment users
**Files**: fix-subdomain-limit.sh, TERMUX_FIX_SUBDOMAIN_LIMIT.md
