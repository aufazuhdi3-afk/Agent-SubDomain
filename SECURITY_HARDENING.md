# Production Security Hardening Guide - Unnar Domain Service

## Overview
This guide covers security best practices and hardening steps required for production deployment of the Unnar Domain Service.

---

## 1. Application Security

### A. Environment Configuration

```php
// .env.production
APP_ENV=production
APP_DEBUG=false  // NEVER set to true in production
APP_URL=https://domains.unnar.id

// Secure random 32-character string
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

TRUSTED_PROXIES=10.0.0.0/8,172.16.0.0/12,192.168.0.0/16
```

### B. Laravel Configuration

```bash
# Cache all configurations (improves performance + security)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# NEVER cache .env in production, as it prevents ENV changes
```

### C. CORS Security

Update `config/cors.php`:
```php
'allowed_origins' => ['https://unnar.id', 'https://*.unnar.id'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

### D. API Rate Limiting

Update routes:
```php
// routes/web.php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/domains', 'DomainController@store');
});

// For admin endpoints (lower limit)
Route::middleware(['auth', 'admin', 'throttle:30,1'])->group(function () {
    Route::post('/admin/domains/{id}/approve', 'AdminDomainController@approve');
});
```

### E. Security Headers

Update `config/app.php` or middleware:
```php
// app/Http/Middleware/SecurityHeaders.php
Header::set('X-Content-Type-Options', 'nosniff');
Header::set('X-Frame-Options', 'DENY');
Header::set('X-XSS-Protection', '1; mode=block');
Header::set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
Header::set('Content-Security-Policy', "default-src 'self'; 
  script-src 'self' 'unsafe-inline' cdn.example.com;
  style-src 'self' 'unsafe-inline';
  img-src 'self' data: https:;
  font-src 'self' data:;
  connect-src 'self';
  frame-ancestors 'none'");
Header::set('Referrer-Policy', 'strict-origin-when-cross-origin');
Header::set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
```

---

## 2. Database Security

### A. User Account Security

```sql
-- Create application user with minimal permissions
CREATE USER 'unnar_app'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT SELECT, INSERT, UPDATE, DELETE ON unnar_domains.* TO 'unnar_app'@'localhost';

-- Create read-only user for backups
CREATE USER 'unnar_reader'@'localhost' IDENTIFIED BY 'STRONG_READONLY_PASSWORD';
GRANT SELECT ON unnar_domains.* TO 'unnar_reader'@'localhost';

-- Never use root for application access
FLUSH PRIVILEGES;
```

### B. Database Encryption

For sensitive fields in migration:
```php
// In migration
Schema::table('domains', function (Blueprint $table) {
    $table->encrypted()->change(); // For Laravel 8+
});
```

### C. Connection Security

```php
// .env.production
DB_CONNECTION=mysql
DB_HOST=localhost  // Use localhost or private IP, not public
DB_PORT=3306
DB_DATABASE=unnar_domains
DB_USERNAME=unnar_app
DB_PASSWORD=STRONG_PASSWORD
DB_SSL_MODE=require  // For cloud databases
```

---

## 3. API Security

### A. RADNET API Integration

```php
// config/services.php
'radnet' => [
    'url' => env('RADNET_API_URL'),
    'key' => env('RADNET_API_KEY'),
    'secret' => env('RADNET_API_SECRET'),
    'timeout' => 30,
],

// Store sensitive data in secrets manager, not .env
```

### B. API Request Signing

```php
// app/Services/RadnetDnsService.php
private function generateSignature($payload): string
{
    return hash_hmac('sha256', json_encode($payload), config('services.radnet.secret'));
}

// Verify API responses
private function verifyResponse($response): bool
{
    $signature = $response->header('X-Signature');
    $body = $response->getContent();
    
    return hash_equals(
        $signature,
        hash_hmac('sha256', $body, config('services.radnet.secret'))
    );
}
```

### C. API Rate Limiting

```php
// Implement per-user rate limiting
Route::middleware('throttle:100,1')->group(function () {
    Route::apiResource('domains', 'DomainController');
});
```

---

## 4. Authentication Security

### A. Password Policy

```php
// app/Models/User.php
protected $rules = [
    'password' => [
        'required', 
        'string', 
        'min:12',
        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        'confirmed',
    ],
];
```

### B. Two-Factor Authentication

```bash
# Install laravel/fortify for 2FA support
composer require laravel/fortify

# Configure in 2FA configuration
```

### C. Session Security

```php
// config/session.php
'secure' => true,  // Only send over HTTPS
'http_only' => true,  // Only accessible via HTTP, not JavaScript
'same_site' => 'strict',  // CSRF protection
'lifetime' => 120,  // 2 hours
```

---

## 5. File Upload Security

```php
// app/Http/Requests/DomainRequest.php
public function rules()
{
    return [
        'target_ip' => ['required', 'ip'],
        'certificate' => [
            'nullable',
            'file',
            'mimes:pem,crt',
            'max:1024',  // 1MB max
        ],
    ];
}

// Validate uploads
$validated = $request->validate($rules);

// Move to secure location outside public_html
$path = $request->file('certificate')
    ->store('certificates', 'private');
```

---

## 6. Server Security

### A. SSH Hardening

```bash
# /etc/ssh/sshd_config
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
Port 22222  # Change from default
LogLevel VERBOSE
MaxAuthTries 3
MaxSessions 10
ClientAliveInterval 300
ClientAliveCountMax 2
```

### B. Firewall Configuration

```bash
sudo ufw enable
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22222/tcp  # SSH on custom port
sudo ufw allow 80/tcp     # HTTP
sudo ufw allow 443/tcp    # HTTPS
sudo ufw status verbose
```

### C. Fail2Ban Setup

```bash
sudo apt install -y fail2ban

# /etc/fail2ban/jail.local
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
```

### D. ModSecurity (WAF)

```bash
# For Apache
sudo apt install -y libapache2-mod-security2

# For Nginx, use ModSecurity-nginx
```

---

## 7. SSL/TLS Configuration

### A. Certificate Setup

```bash
# Install Let's Encrypt
sudo apt install -y certbot python3-certbot-nginx

# Generate certificate with OCSP stapling
sudo certbot certonly --nginx -d domains.unnar.id --staple-ocsp

# Auto-renewal
sudo systemctl enable certbot.timer
```

### B. SSL/TLS Configuration

```
# For Nginx
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:..;
ssl_prefer_server_ciphers on;
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;
ssl_stapling on;
ssl_stapling_verify on;
```

### C. HSTS

```
# Add to headers
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
```

---

## 8. Secret Management

### A. Environment Variables

Never commit secrets to version control:
```bash
# .gitignore
.env
.env.production
.env.*.local
storage/
```

### B. Secrets Manager Integration

For cloud deployment:
```php
// Using AWS Secrets Manager
$secret = \Aws\SecretsManager\SecretsManagerClient::getSecretValue([
    'SecretId' => 'unnar/api-key'
]);
```

### C. Key Rotation

Implement regular rotation:
```bash
# Rotate application key
php artisan app:key-rotate --force

# Update API keys monthly
# Update database passwords quarterly
```

---

## 9. Audit Logging

### A. Activity Logging

```php
// Ensure all critical actions are logged
activity_log($userId, 'domain_approved', [
    'domain_id' => $domain->id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

### B. Database Audit Trail

```php
// app/Models/Domain.php
protected $casts = [
    'ip_address' => 'encrypted',
    'user_id' => 'encrypted',
];

// Track changes
protected static function booted()
{
    static::updated(function ($model) {
        Log::info('Domain updated', [
            'domain_id' => $model->id,
            'changes' => $model->getChanges(),
        ]);
    });
}
```

### C. Access Logging

```php
// Log all API access
Route::middleware(['api', 'log-access'])->group(function () {
    // API routes
});
```

---

## 10. Incident Response

### A. Log Monitoring

```bash
# Monitor for suspicious activity
tail -f /var/www/unnar-domains/storage/logs/laravel.log | grep -i error

# Check failed login attempts
grep "Failed login" /var/log/auth.log
```

### B. Emergency Response

```bash
# If compromised:
1. Isolate the server from network
2. Change all passwords and API keys
3. Review audit logs for unauthorized access
4. Restore from known-good backup
5. Deploy patched version
```

### C. Notification Setup

```php
// Send alerts for critical events
Notification::route('slack', config('services.slack.security_channel'))
    ->notify(new SecurityAlert('Suspicious activity detected'));
```

---

## 11. Regular Security Tasks

### Weekly
- [ ] Review error logs
- [ ] Monitor activity logs
- [ ] Check failed login attempts
- [ ] Verify backups completed

### Monthly
- [ ] Security patch updates
- [ ] Review user permissions
- [ ] Audit API access logs
- [ ] Check SSL certificate expiration (30 days before)

### Quarterly
- [ ] Full security audit
- [ ] Penetration testing
- [ ] Change API keys/secrets
- [ ] Review database users

### Annually
- [ ] Security assessment by external firm
- [ ] Update security policies
- [ ] Disaster recovery drill

---

## 12. Compliance Checklist

- [ ] GDPR compliant (if EU users)
  - [ ] Privacy policy posted
  - [ ] Consent for data collection
  - [ ] Right to access implemented
  - [ ] Data deletion capability
  
- [ ] ISO 27001 readiness
  - [ ] Information security policy
  - [ ] Access control procedures
  - [ ] Incident response plan
  
- [ ] NIST guidelines
  - [ ] Identify critical assets
  - [ ] Protect with controls
  - [ ] Detect anomalies
  - [ ] Respond to incidents
  - [ ] Recover from breaches

---

## Security Resources

- OWASP Top 10: https://owasp.org/Top10/
- Laravel Security: https://laravel.com/docs/security
- NIST Cybersecurity Framework: https://www.nist.gov/cyberframework
- CIS Benchmarks: https://www.cisecurity.org/
- Mozilla Security Guidelines: https://infosec.mozilla.org/

