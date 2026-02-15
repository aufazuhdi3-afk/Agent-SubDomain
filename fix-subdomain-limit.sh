#!/bin/bash
# 🔧 Auto-Fix Script: Add missing subdomain_limit to Termux Database
# Run this in Termux: bash fix-subdomain-limit.sh

set -e

echo "🔍 Checking database migration status..."
echo ""

# Navigate to project root
cd "$(dirname "$0")" || exit 1

# Check if database exists
if [ ! -f "database/database.sqlite" ]; then
    echo "❌ Database file not found at database/database.sqlite"
    exit 1
fi

echo "📊 Current migration status:"
php artisan migrate:status | grep "subdomain_limit"
echo ""

# Check if column exists
echo "🔎 Checking if 'subdomain_limit' column exists in users table..."
COLUMN_EXISTS=$(sqlite3 database/database.sqlite ".schema users" | grep -c "subdomain_limit" || echo "0")

if [ "$COLUMN_EXISTS" -gt 0 ]; then
    echo "✅ Column 'subdomain_limit' already exists! No migration needed."
    echo ""
    echo "Column details:"
    sqlite3 database/database.sqlite ".schema users" | grep -A2 "subdomain_limit"
    exit 0
fi

echo "⚠️  Column 'subdomain_limit' NOT found. Running migration..."
echo ""

# Backup database
echo "💾 Backing up database to database/database.sqlite.backup..."
cp database/database.sqlite database/database.sqlite.backup
echo "✅ Backup created!"
echo ""

# Run migration
echo "🚀 Running migration: add_subdomain_limit_to_users_table..."
php artisan migrate

echo ""
echo "✅ Migration completed!"
echo ""

# Verify column exists now
echo "🔎 Verifying column was added..."
if sqlite3 database/database.sqlite ".schema users" | grep -q "subdomain_limit"; then
    echo "✅ SUCCESS! Column 'subdomain_limit' is now in users table:"
    echo ""
    sqlite3 database/database.sqlite "PRAGMA table_info(users);" | grep subdomain_limit
    echo ""
    echo "📊 All users data (with subdomain limits):"
    sqlite3 database/database.sqlite "SELECT id, name, email, subdomain_limit FROM users;"
    echo ""
    echo "✅ Fix completed! You can now:"
    echo "   1. Refresh the admin panel"
    echo "   2. Edit users without errors"
    echo "   3. Update subdomain limits"
else
    echo "❌ Column still not found. Something went wrong."
    echo "🔄 Restoring backup..."
    cp database/database.sqlite.backup database/database.sqlite
    exit 1
fi
