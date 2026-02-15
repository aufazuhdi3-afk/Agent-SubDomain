# 🎯 How to Fix: Termux Database Error - Step-by-Step

## Problem Summary
```
❌ SQLSTATE[HY000]: General error: 1 no such column: subdomain_limit
```

Terjadi saat: Admin mencoba update user profile
Lokasi error: `/admin/users/2` (Edit user form)
Penyebab: Migration belum di-run di Termux database

---

## 🚀 Solution (Pilih Salah Satu)

### ✨ **OPTION 1: Auto-Fix Script** (Mudah & Aman)

**Di Termux:**

```bash
# 1. SSH ke Termux (dari PC)
ssh -p 8022 localhost

# 2. Go ke project folder
cd ~/Agent-SubDomain

# 3. Run fix script
bash fix-subdomain-limit.sh
```

**Expected Output:**
```
🔍 Checking database migration status...
⚠️ Column 'subdomain_limit' NOT found. Running migration...
💾 Backing up database to database/database.sqlite.backup...
✅ Backup created!
🚀 Running migration: add_subdomain_limit_to_users_table...
✅ Migration completed!
✅ SUCCESS! Column 'subdomain_limit' is now in users table:
...
```

✅ **Done!** Script akan handle semuanya.

---

### ⚡ **OPTION 2: Simple Command**

**Di Termux:**

```bash
cd ~/Agent-SubDomain
php artisan migrate
```

Hanya 1 command! Script akan auto-detect dan run migration yang pending.

---

### 🔧 **OPTION 3: Manual Verification**

**Di Termux:**

```bash
# Check status
php artisan migrate:status

# Manually run if needed
php artisan migrate

# Verify column exists
sqlite3 database/database.sqlite ".schema users"
```

---

## ✔️ Verify Fix Success

**Di Termux:**

```bash
# Check kolom ada
sqlite3 database/database.sqlite "PRAGMA table_info(users);"
```

Cari output yang berisi:
```
6|subdomain_limit|integer||0|3
```

Atau command lainnya:
```bash
sqlite3 database/database.sqlite "SELECT id, name, subdomain_limit FROM users;"
```

Output:
```
1|admin@unnar.id|3
2|test@example.com|3
```

---

## 🌐 Test After Fix

**Di Browser:**

1. Buka: `http://localhost:8000/admin/users`
2. Klik **Edit** di salah satu user
3. Di form, coba ubah **"Subdomain Limit"**
4. Klik **Save**

**Expected:** ✅ Form save berhasil tanpa error!

---

## 🆘 Jika Masih Error

### Debug Step 1: Check Migration
```bash
php artisan migrate:status | grep subdomain_limit
```

Harus show status `[2] Ran` atau similar

### Debug Step 2: Clear Cache
```bash
php artisan config:cache
php artisan cache:clear
php artisan route:cache
```

### Debug Step 3: Fresh Database (Last Resort)
```bash
# Backup dulu!
cp database/database.sqlite database/database.sqlite.backup

# Reset
rm database/database.sqlite
php artisan migrate
php artisan db:seed

# Login: admin@unnar.id / password
```

---

## 📊 Timeline

| Waktu | Event |
|-------|-------|
| 2026-02-13 | Database seeded (tapi belum ada subdomain_limit) |
| 2026-02-15 | Migration file dibuat |
| 2026-02-15 | Workspace migration di-run ✅ |
| 2026-02-15 | Documentation pushed ke GitHub |
| NOW | Anda fix di Termux |

---

## 🎓 Why Migration di Termux Perlu di-Run Sendiri?

1. Workspace dan Termux = **2 database berbeda**
2. Setiap environment perlu migration di-run
3. Tidak auto-sync πjika manual sync file

**Solution:** Selalu run `php artisan migrate` saat:
- ✅ Git pull kode baru
- ✅ Mulai development session baru
- ✅ Terima error "no such column"

---

## 📚 Helpful Commands

```bash
# Koneksi database Termux
sqlite3 ~/Agent-SubDomain/database/database.sqlite

# List all tables
.tables

# Show users table schema
.schema users

# Exit sqlite
.quit

# Check Laravel version
php artisan --version

# Test migration rollback (jangan di production!)
php artisan migrate:rollback --step=1
```

---

## ✅ Success Indicators

Setelah fix, Anda bisa:

- ✅ Edit user profiles tanpa error
- ✅ Set/change subdomain limits
- ✅ See quota progress bar di user dashboard
- ✅ Create domains dengan respect ke per-user limits
- ✅ Admin manage user quotas

---

## 📞 Questions?

Lihat file lengkap:
- `TERMUX_FIX_SUBDOMAIN_LIMIT.md` - Detailed troubleshooting
- `QUICK_FIX_SUBDOMAIN.md` - Quick reference
- `fix-subdomain-limit.sh` - Auto script

---

**Last Updated:** 2026-02-15
**Status:** ✅ Ready to implement
**Difficulty:** ⭐ Beginner-Friendly (1-2 minutes)
