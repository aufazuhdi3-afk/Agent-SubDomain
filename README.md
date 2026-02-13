# Unnar Domain Service

A production-ready Domain Provisioning Service for unnar.id where campus users can request subdomains and IT admins approve and provision them automatically via the RADNET DNS API.

## Features

- **User Domain Management**: Users can request up to 3 subdomains with a limit of 3 requests per day
- **Admin Dashboard**: Administrators review, approve, reject, and manage domains
- **Automatic Provisioning**: Queue-based system that automatically provisions approved domains via RADNET API
- **Activity Logging**: Complete audit trail of all user and admin actions
- **Email Notifications**: Users receive notifications when domains are approved, active, or fail provisioning
- **Role-Based Access Control**: Secure admin and user routes with middleware
- **Queue System**: Database-backed queue for reliable job processing

## Requirements

- PHP 8.2+
- Laravel 12
- SQLite or MySQL
- Composer
- Node.js 16+
- npm

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd unnar-domain-service
```

### 2. Install Dependencies

```bash
composer install
npm install
npm run build
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure:

```dotenv
APP_NAME="Unnar Domain Service"
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# or
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=unnar_domains
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=log
# For production, configure a real mailer (smtp, mailgun, etc.)

QUEUE_CONNECTION=database

# RADNET API Configuration
RADNET_API_URL=https://api.radnet.id
RADNET_API_KEY=your_radnet_api_key_here
```

### 4. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

This will:
- Create all required tables
- Create an admin user with credentials:
  - Email: `admin@unnar.id`
  - Password: `password`

## Running the Application

### Local Development

Start all required services in one command:

```bash
composer run dev
```

This will run:
- PHP development server (port 8000)
- Queue listener
- Tail logs (pail)
- Vite development server

Alternatively, run services individually:

```bash
# Terminal 1: PHP Server
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:listen --tries=1 --timeout=0

# Terminal 3: Vite Dev Server
npm run dev
```

### Access the Application

- **Application**: http://localhost:8000
- **Admin Dashboard**: http://localhost:8000/admin/domains
- **User Dashboard**: http://localhost:8000/dashboard

**Admin Login:**
- Email: `admin@unnar.id`
- Password: `password`

## User Workflow

1. **Register/Login**: Users create an account or log in
2. **Request Domain**: Navigate to `/domains/create` to request a new subdomain
   - Enter subdomain name (lowercase, alphanumeric, hyphens only)
   - Provide target IP address
3. **Await Approval**: Admin reviews the request on the admin dashboard
4. **Domain Active**: Once approved, the domain is provisioned via RADNET API
5. **Manage Domains**: Users can view all their domains at `/domains`

## Admin Workflow

1. **Login**: Admin logs in with admin credentials
2. **View Requests**: Navigate to `/admin/domains` to see pending domain requests
3. **Take Action**:
   - **Approve**: Starts automatic provisioning via RADNET API
   - **Reject**: Marks domain as failed
   - **Suspend**: Suspends an active domain
   - **Retry**: Retries failed provisioning

## API Integration

The application integrates with RADNET DNS API for automatic subdomain provisioning.

### RADNET API Endpoints Used

- `POST /api/dns/create`: Create a new subdomain
- `POST /api/dns/delete`: Delete a subdomain
- `POST /api/dns/update`: Update subdomain target IP

### Configuration

Set the following environment variables:

```
RADNET_API_URL=https://api.radnet.id
RADNET_API_KEY=your_api_key
```

## Queue System

The application uses a database-backed queue system for reliable job processing.

### Processing Jobs

```bash
php artisan queue:listen --tries=3
```

The `CreateDomainJob` handles domain provisioning with:
- 3 retry attempts
- Exponential backoff: 10s, 1m, 5m
- Automatic status updates
- Detailed error logging

## Activity Logging

All user and admin actions are logged in the `activity_logs` table, including:
- Domain requests
- Admin approvals/rejections
- Provisioning success/failure
- Domain suspensions

Access logs programmatically:

```php
$logs = \App\Models\ActivityLog::where('user_id', $userId)->latest()->get();
```

## Email Notifications

Users receive notifications when:
- Their domain request is approved
- Their domain becomes active
- Provisioning fails

Configure email driver in `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@unnar.id
MAIL_FROM_NAME="Unnar Domain Service"
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User name
- `email` - Unique email
- `password` - Hashed password
- `role` - ENUM: admin, user (default: user)
- `email_verified_at` - Email verification timestamp
- `timestamps` - Created/updated timestamps

### Domains Table
- `id` - Primary key
- `user_id` - FK to users
- `subdomain` - Unique subdomain name
- `full_domain` - Complete domain (subdomain.unnar.id)
- `target_ip` - IPv4/IPv6 target address
- `status` - ENUM: pending, approved, provisioning, active, failed, suspended
- `radnet_response` - JSON API response
- `timestamps` - Created/updated timestamps

### Activity Logs Table
- `id` - Primary key
- `user_id` - FK to users
- `action` - Action name
- `description` - Action details
- `ip_address` - Client IP address
- `timestamps` - Created/updated timestamps

## Production Deployment

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-curl \
    php8.2-gd php8.2-xml composer npm mysql-server
```

### 2. Application Deploy

```bash
git clone <repository-url> /var/www/unnar-domain-service
cd /var/www/unnar-domain-service

composer install --no-dev
npm install && npm run build

cp .env.example .env
php artisan key:generate
php artisan config:cache
php artisan route:cache

php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
```

### 3. Web Server Configuration

**Nginx Configuration:**

```nginx
server {
    listen 80;
    server_name domains.unnar.id;

    root /var/www/unnar-domain-service/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/unnar-domain-queue.conf`:

```ini
[program:unnar-domain-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/unnar-domain-service/artisan queue:work database --tries=3
autostart=true
autorestart=true
stopasgroup=true
stopwaitsecs=60
numprocs=4
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/unnar-domain-queue.log
```

Update Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start unnar-domain-queue:*
```

### 5. SSL Certificate (Let's Encrypt)

```bash
sudo certbot certonly --standalone -d domains.unnar.id
```

Update Nginx to use HTTPS and redirect HTTP traffic.

### 6. Scheduled Tasks (Cron)

Add to crontab:

```bash
* * * * * cd /var/www/unnar-domain-service && php artisan schedule:run >> /dev/null 2>&1
```

## Testing

```bash
php artisan test
```

Run with coverage:

```bash
php artisan test --coverage
```

## Troubleshooting

### Migrations Not Running
```bash
php artisan migrate:refresh  # WARNING: Resets database
php artisan migrate:status   # Check migration status
```

### Queue Jobs Not Processing
```bash
php artisan queue:failed     # View failed jobs
php artisan queue:retry all  # Retry all failed jobs
```

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/unnar-domain-service
sudo chmod -R 755 /var/www/unnar-domain-service
sudo chmod -R 775 storage/ bootstrap/cache/
```

## Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Breeze](https://laravel.com/docs/breeze)
- [Laravel Queues](https://laravel.com/docs/queues)

## Contributing

Please follow the Laravel coding standards and submit pull requests with clear descriptions.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please contact: support@unnar.id

---

**Last Updated:** February 12, 2026
