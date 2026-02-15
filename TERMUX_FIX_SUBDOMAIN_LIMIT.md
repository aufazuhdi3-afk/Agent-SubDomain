# 🔧 Fix: Missing subdomain_limit Column in Termux

## Problem
Database Termux tidak memiliki kolom `subdomain_limit` di tabel `users`. Error ini terjadi saat admin mencoba update user.

**Error:**
```
SQLSTATE[HY000]: General error: 1 no such column: subdomain_limit
```

**Root Cause:** Migration `2026_02_15_185419_add_subdomain_limit_to_users_table` belum di-run di Termux.

---

## Solution: Run Migration di Termux

### Step 1: SSH ke Termux (dari PC)
```bash
ssh -p 8022 localhost
# atau jika sudah di Termux, lanjut ke Step 2
```

### Step 2: Navigate ke Project Directory
```bash
cd ~/Agent-SubDomain
```

### Step 3: Run Migration Lengkap
```bash
# Jika fresh install (tidak ada migration sebelumnya):
php artisan migrate

# Jika sudah ada migrasi sebelumnya tapi tertinggal subdomain_limit:
php artisan migrate --step
```

### Step 4: Verify Kolom Sudah Ada
```bash
sqlite3 database/database.sqlite ".schema users"
```

Di output, Anda seharusnya melihat:
```sql
CREATE TABLE users(
  ...
  "role" varchar NOT NULL DEFAULT 'user',
  "subdomain_limit" integer DEFAULT 3,
  ...
)
```

### Step 5: Test Admin User Update
Buka browser dan coba:
- Buka: `http://localhost/admin/users`
- Edit salah satu user
- Coba ubah subdomain limit
- Klik "Save" → Seharusnya berhasil ✅

---

## Alternative: Fresh Database Reset

Jika migrasi masih bermasalah, reset database sepenuhnya:

```bash
# BACKUP DULU!
cp database/database.sqlite database/database.sqlite.backup

# Hapus database lama
rm database/database.sqlite

# Re-run semua migration
php artisan migrate

# Seed data (create admin user)
php artisan db:seed
```

**Admin credentials setelah seed:**
- Email: `admin@unnar.id`
- Password: `password`

---

## Verify Migration di Workspace (Optional)

Migration sudah di-push ke GitHub. Di workspace:

```bash
php artisan migrate:status
```

Output sudah menunjukkan:
```
2026_02_15_185419_add_subdomain_limit_to_users_table ............... [2] Ran
```

---

## Quick Commands Cheat Sheet

```bash
# Check migration status
php artisan migrate:status

# Run all pending migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Rollback specific migration
php artisan migrate:rollback --step=1 --batch=2

# Seed database (create admin user)
php artisan db:seed

# Check database schema
sqlite3 database/database.sqlite ".schema users"

# Get all users with subdomain_limit
sqlite3 database/database.sqlite "SELECT id, name, email, subdomain_limit FROM users;"
```

---

## Description Kolom subdomain_limit

| Value | Meaning |
|-------|---------|
| `NULL` | User memiliki unlimited subdomain (∞) |
| `3` | User bisa membuat max 3 subdomain (default) |
| `5+` | Custom limit (bisa disesuaikan admin) |

Admin bisa mengubah limit ini di form edit user: `/admin/users/{id}/edit`

---

## Status
- ✅ Migration file exists: `database/migrations/2026_02_15_185419_add_subdomain_limit_to_users_table.php`
- ✅ Code sudah di-push ke GitHub
- ⚠️ Perlu di-run di Termux database

**After fix:** Admin user update akan langsung work! ✅
