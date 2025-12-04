# LiveNetStudios Deployment Guide

## 🎯 Production Deployment

This guide covers deploying the LiveNetStudios booking and payment system to a production server.

## Prerequisites

- Linux server (Ubuntu 20.04+ recommended)
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Node.js 18+ and npm
- Composer
- Web server (Nginx or Apache)
- SSL certificate (Let's Encrypt recommended)
- SMTP email service (Gmail, SendGrid, Mailgun, etc.)

---

## Backend Deployment

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx
```

### 2. Clone and Configure

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/ourstudios.git
sudo chown -R www-data:www-data ourstudios
cd ourstudios/backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Configure environment
cp .env.example .env
nano .env
```

### 3. Environment Configuration

Update `/var/www/ourstudios/backend/.env`:

```env
APP_NAME=LiveNetStudios
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=livenetstudios_prod
DB_USERNAME=livenet_user
DB_PASSWORD=your_secure_password

# Email Configuration (Gmail example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="LiveNetStudios"

# Frontend URL
FRONTEND_URL=https://yourdomain.com

# Admin notification email
ADMIN_EMAIL=admin@yourdomain.com
```

### 4. Database Setup

```bash
# Create database and user
sudo mysql

CREATE DATABASE livenetstudios_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'livenet_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON livenetstudios_prod.* TO 'livenet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations and seeders
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Nginx Configuration

Create `/etc/nginx/sites-available/livenetstudios`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.yourdomain.com;
    
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.yourdomain.com;

    root /var/www/ourstudios/backend/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/api.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.yourdomain.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # File upload size
    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/livenetstudios /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. SSL Certificate

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d api.yourdomain.com
```

---

## Frontend Deployment

### 1. Build Frontend

```bash
cd /var/www/ourstudios/frontend

# Install dependencies
npm install

# Update environment for production
echo "VITE_API_BASE_URL=https://api.yourdomain.com/api" > .env

# Build for production
npm run build
```

### 2. Nginx Configuration for Frontend

Create `/etc/nginx/sites-available/livenetstudios-frontend`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/ourstudios/frontend/dist;
    index index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/livenetstudios-frontend /etc/nginx/sites-enabled/
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
sudo nginx -t
sudo systemctl reload nginx
```

---

## Update CORS Configuration

Update `/var/www/ourstudios/backend/config/cors.php`:

```php
'allowed_origins' => [
    'https://yourdomain.com',
    'https://www.yourdomain.com',
    env('FRONTEND_URL', 'https://yourdomain.com'),
],
```

Clear config cache:

```bash
cd /var/www/ourstudios/backend
php artisan config:clear
php artisan config:cache
```

---

## Security Checklist

- [ ] Change default admin password immediately
- [ ] Update `.env` with strong database password
- [ ] Configure firewall (UFW):
  ```bash
  sudo ufw allow 22/tcp
  sudo ufw allow 80/tcp
  sudo ufw allow 443/tcp
  sudo ufw enable
  ```
- [ ] Set proper file permissions:
  ```bash
  sudo chown -R www-data:www-data /var/www/ourstudios
  sudo chmod -R 755 /var/www/ourstudios
  sudo chmod -R 775 /var/www/ourstudios/backend/storage
  sudo chmod -R 775 /var/www/ourstudios/backend/bootstrap/cache
  ```
- [ ] Disable directory listing in Nginx
- [ ] Set up automated backups
- [ ] Configure fail2ban for SSH protection
- [ ] Enable SQL query logging (temporarily for debugging)

---

## Database Backup

Create automated backup script `/usr/local/bin/backup-livenet.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/livenetstudios"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u livenet_user -p'your_secure_password' livenetstudios_prod > \
    $BACKUP_DIR/db_backup_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

Make executable and add to cron:

```bash
sudo chmod +x /usr/local/bin/backup-livenet.sh
sudo crontab -e

# Add daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-livenet.sh
```

---

## Monitoring

### Set up Laravel Logs

```bash
# View logs
tail -f /var/www/ourstudios/backend/storage/logs/laravel.log

# Rotate logs
sudo nano /etc/logrotate.d/laravel
```

Add:

```
/var/www/ourstudios/backend/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    missingok
}
```

---

## Maintenance Mode

```bash
# Enable maintenance mode
php artisan down --secret="your-secret-token"

# Access site during maintenance
# Visit: https://yourdomain.com/your-secret-token

# Disable maintenance mode
php artisan up
```

---

## Updating the Application

```bash
cd /var/www/ourstudios

# Pull latest changes
sudo git pull origin main

# Backend updates
cd backend
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend updates
cd ../frontend
npm install
npm run build

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```

---

## Troubleshooting

**500 Internal Server Error:**
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Check Nginx error logs: `sudo tail -f /var/log/nginx/error.log`
- Verify file permissions
- Clear all caches: `php artisan cache:clear && php artisan config:clear`

**CORS Errors:**
- Verify CORS origins in `config/cors.php`
- Check FRONTEND_URL in `.env`
- Clear config cache: `php artisan config:cache`

**Email Not Sending:**
- Test SMTP credentials
- Check firewall allows outbound port 587
- Verify MAIL settings in `.env`
- Check spam folder

**Database Connection Failed:**
- Verify MySQL is running: `sudo systemctl status mysql`
- Check credentials in `.env`
- Ensure database exists: `mysql -u root -p -e "SHOW DATABASES;"`

---

## Support

For production support, contact: admin@yourdomain.com
