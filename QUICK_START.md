# Quick Start Guide

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 16+ and npm
- SQLite (included) or MySQL

## Installation (5 minutes)

### Option 1: Automated Setup

```bash
chmod +x setup.sh
./setup.sh
```

### Option 2: Manual Setup

```bash
# 1. Install dependencies
composer install
npm install && npm run build

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate
php artisan db:seed

# 4. Ready to go!
```

## Running the Application

### All-in-One Command (Recommended)

```bash
composer run dev
```

This starts:
- PHP server on http://localhost:8000
- Queue worker for background jobs
- Vite dev server for asset compilation
- Live log tail

### Manual Setup (3 terminals)

**Terminal 1 - Web Server:**
```bash
php artisan serve
```
Access: http://localhost:8000

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:listen --tries=1
```

**Terminal 3 - Asset Compilation:**
```bash
npm run dev
```

## First Login

**Admin Account:**
- Email: `admin@unnar.id`
- Password: `password`

**Test User Account:**
- Email: `test@example.com`
- Password: `password`

## Key Routes

### For Users
- `/domains` - View my domains
- `/domains/create` - Request new domain
- `/dashboard` - User dashboard

### For Admins
- `/admin/domains` - Manage all domains
- `/admin/domains?status=pending` - View pending requests
- `/admin/domains?status=active` - View active domains

## Configuration

### Edit `.env` file:

```dotenv
# Application
APP_NAME="Unnar Domain Service"
APP_URL=http://localhost:8000

# Database (uses SQLite by default)
DB_CONNECTION=sqlite

# Queue (database queue is configured)
QUEUE_CONNECTION=database

# RADNET API (add your credentials)
RADNET_API_URL=https://api.radnet.id
RADNET_API_KEY=your_api_key_here

# Mail (log driver for testing)
MAIL_MAILER=log
```

## Common Commands

```bash
# View database logs
php artisan pail

# Reset database (WARNING: loses all data)
php artisan migrate:refresh --seed

# Run tests
php artisan test

# Create new user for testing
php artisan tinker
> \App\Models\User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password'), 'role' => 'user'])

# Check queue jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

## Project Structure

```
app/
├── Controllers/
│   ├── DomainController.php        # User domain operations
│   ├── AdminDomainController.php   # Admin domain management
│   └── ProfileController.php       # User profile
├── Models/
│   ├── User.php                    # User with role
│   ├── Domain.php                  # Domain model
│   └── ActivityLog.php             # Audit logs
├── Jobs/
│   └── CreateDomainJob.php         # Queue job for provisioning
├── Services/
│   └── RadnetDnsService.php        # RADNET API integration
├── Mail/
│   └── NotificationMail.php        # Email notifications
└── Helpers/
    └── AppHelpers.php              # Helper functions

resources/
├── views/
│   ├── domains/
│   │   ├── index.blade.php         # User domains list
│   │   └── create.blade.php        # Request domain form
│   ├── admin/domains/
│   │   └── index.blade.php         # Admin dashboard
│   ├── emails/
│   │   └── notification.blade.php  # Email template
│   └── layouts/                    # Layout templates

database/
├── migrations/
│   ├── add_role_to_users_table.php
│   ├── create_domains_table.php
│   └── create_activity_logs_table.php
└── seeders/
    ├── AdminSeeder.php
    └── DatabaseSeeder.php

routes/
└── web.php                         # Web routes with admin prefix
```

## Features Implemented

✅ User authentication (Breeze)
✅ Role-based access control (admin/user)
✅ Domain model with relationships
✅ User domain request form
✅ Admin approval workflow
✅ Queue-based async provisioning
✅ RADNET API integration
✅ Activity logging
✅ Email notifications
✅ Dashboard with stats
✅ Pagination
✅ Flash messages
✅ Form validation
✅ Rate limiting (3 requests/day)
✅ Domain limit (3 per user)

## Troubleshooting

### Queue jobs not running?
```bash
# Start queue worker
php artisan queue:listen

# Check database for queued jobs
php artisan tinker
> \DB::table('jobs')->get()
```

### Database errors?
```bash
# Check migrations
php artisan migrate:status

# Fresh start (careful!)
php artisan migrate:refresh --seed
```

### Port 8000 already in use?
```bash
# Use different port
php artisan serve --port=8080
```

### Files not building?
```bash
# Clear cache and rebuild
npm run build
php artisan view:clear
php artisan config:clear
```

## Production Deployment

See README.md for complete production deployment guide with:
- Server setup
- Nginx configuration
- SSL certificates
- Supervisor queue workers
- Database backups
- Monitoring

## Support

For issues or questions, refer to:
- README.md - Full documentation
- Laravel docs: https://laravel.com/docs
- This project on GitHub

---

Happy coding! 🚀
