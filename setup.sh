#!/bin/bash

# Unnar Domain Service - Quick Setup Script
# This script helps set up the application on a new system

set -e

echo "=========================================="
echo "UNNAR DOMAIN SERVICE - SETUP"
echo "=========================================="
echo ""

# Check if composer and npm are installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install Node.js and npm first."
    exit 1
fi

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install

echo "📦 Installing npm dependencies..."
npm install

# Build assets
echo "🏗️  Building frontend assets..."
npm run build

# Environment setup
if [ ! -f .env ]; then
    echo "🔧 Setting up .env file..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ .env file created and APP_KEY generated"
    echo "   Edit .env file to configure database and RADNET API credentials"
fi

# Database setup
echo "📊 Setting up database..."
php artisan migrate
php artisan db:seed

echo ""
echo "=========================================="
echo "SETUP COMPLETE!"
echo "=========================================="
echo ""
echo "✅ Database migrations completed"
echo "✅ Admin user created"
echo "   Email: admin@unnar.id"
echo "   Password: password"
echo ""
echo "🚀 To start the development server, run:"
echo "   php artisan serve"
echo ""
echo "📝 In another terminal, start the queue worker:"
echo "   php artisan queue:listen"
echo ""
echo "🎨 In a third terminal, start the Vite dev server:"
echo "   npm run dev"
echo ""
echo "📖 For more information, see README.md"
echo "=========================================="
