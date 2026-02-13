#!/bin/bash
# Production Database Setup Script for Unnar Domain Service
# This script sets up MySQL database and user for production
# Run this on your production MySQL server

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Unnar Domain Service - Production Database Setup ===${NC}\n"

# Database configuration
DB_NAME="unnar_domains"
DB_USER="unnar_app"
DB_HOST="localhost"
READ_ONLY_USER="unnar_reader"

# Prompt for password
read -s -p "Enter password for database user '$DB_USER': " DB_PASSWORD
echo ""
read -s -p "Confirm password: " DB_PASSWORD_CONFIRM
echo ""

if [ "$DB_PASSWORD" != "$DB_PASSWORD_CONFIRM" ]; then
    echo -e "${RED}Passwords do not match!${NC}"
    exit 1
fi

read -s -p "Enter password for read-only user '$READ_ONLY_USER': " READONLY_PASSWORD
echo ""

# Generate SQL commands
SQL_COMMANDS="
-- Drop existing database if exists (BE CAREFUL!)
-- DROP DATABASE IF EXISTS $DB_NAME;

-- Create database with UTF-8 encoding
CREATE DATABASE IF NOT EXISTS $DB_NAME 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Create application user with full privileges
CREATE USER IF NOT EXISTS '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_PASSWORD';

-- Grant all privileges to application user
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'$DB_HOST';

-- Create read-only user for backups
CREATE USER IF NOT EXISTS '$READ_ONLY_USER'@'$DB_HOST' IDENTIFIED BY '$READONLY_PASSWORD';

-- Grant read-only privileges
GRANT SELECT ON $DB_NAME.* TO '$READ_ONLY_USER'@'$DB_HOST';

-- Apply changes
FLUSH PRIVILEGES;

-- Show created users
SELECT User, Host FROM mysql.user WHERE User IN ('$DB_USER', '$READ_ONLY_USER');
"

echo -e "${YELLOW}Executing database setup...${NC}\n"

# Execute SQL commands
mysql -u root -p << EOF
$SQL_COMMANDS
EOF

if [ $? -eq 0 ]; then
    echo -e "\n${GREEN}✓ Database and users created successfully!${NC}\n"
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Update your .env.production file with:"
    echo "   DB_HOST=$DB_HOST"
    echo "   DB_DATABASE=$DB_NAME"
    echo "   DB_USERNAME=$DB_USER"
    echo "   DB_PASSWORD=$DB_PASSWORD"
    echo ""
    echo "2. Run Laravel migrations:"
    echo "   php artisan migrate --env=production"
    echo ""
    echo "3. Seed admin user (if needed):"
    echo "   php artisan db:seed --env=production"
else
    echo -e "${RED}✗ Database setup failed!${NC}"
    exit 1
fi
