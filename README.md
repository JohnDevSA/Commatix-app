# Commatix

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Laravel](https://img.shields.io/badge/laravel-12.x-red)
![Filament](https://img.shields.io/badge/admin-filament%203.x-orange)
![PHP](https://img.shields.io/badge/php-^8.2-blue)
![Tenancy](https://img.shields.io/badge/multi--tenant-stancl%203.5-green)

> **Commatix** is a modern, multi-tenant communication platform built with Laravel and enhanced with Filament Admin, queue workers, and dynamic workflow handling.

---

## 🧱 Stack Overview

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament v3
- **Multi-Tenancy**: [stancl/tenancy](https://github.com/stancl/tenancy)
- **Role & Permission Management**: [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
- **Email Integration**: [Resend Laravel](https://github.com/resend/resend-laravel)
- **SMS Integration**: [Vonage Client](https://github.com/Vonage/vonage-php-sdk-core)
- **Scaffolding & Developer Tools**:
    - Laravel Blueprint
    - Laravel Pint (code formatting)
    - Laravel Pail (debugging)
    - PHPUnit (testing)
    - Faker (test data)

---

## 🚀 Getting Started

Commatix supports two development environments: **Docker (recommended)** or **Local**.

---

## 🐳 Docker Setup (Recommended)

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop) (with WSL2 integration on Windows)
- Git
- pnpm (or npm)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/commatix.git
   cd commatix
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Initialize Laravel Sail**
   ```bash
   php artisan sail:install
   ```

   When prompted, select the services you need:
   - ✅ **mysql** (required for database)
   - ✅ **redis** (required for queues/cache)
   - ⚪ **mailpit** (optional - for email testing)
   - ⚪ **meilisearch** (optional - for search)

4. **Copy environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Start Docker containers**
   ```bash
   ./vendor/bin/sail up -d
   ```

   This will start MySQL, Redis, and your Laravel application in Docker containers.

6. **Run database migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

7. **Install frontend dependencies and start dev server**
   ```bash
   pnpm install
   pnpm dev
   ```

8. **Access the application**
   - Main app: http://localhost
   - Admin panel: http://localhost/admin
   - Mailpit (if installed): http://localhost:8025

### Useful Sail Commands

```bash
# Stop all containers
./vendor/bin/sail down

# View logs
./vendor/bin/sail logs -f

# Run artisan commands
./vendor/bin/sail artisan [command]

# Run composer commands
./vendor/bin/sail composer [command]

# Access MySQL CLI
./vendor/bin/sail mysql

# Run tests
./vendor/bin/sail artisan test

# Create bash alias (optional - add to ~/.bashrc or ~/.zshrc)
alias sail='./vendor/bin/sail'
```

---

## 💻 Local Setup (Without Docker)

### Prerequisites

- PHP 8.2 or later
- Composer
- Node.js & pnpm
- MySQL or SQLite
- Redis (for queues)
- Git

### Installation

```bash
git clone https://github.com/your-org/commatix.git
cd commatix

composer install
cp .env.example .env
php artisan key:generate

pnpm install
pnpm dev
```

---

## 🐛 Troubleshooting

### WSL/Ubuntu: Apache2 Port Conflict

**Problem**: When accessing `http://localhost`, you see the default Apache2 page instead of Commatix.

**Cause**: Ubuntu/WSL often comes with Apache2 pre-installed and running on port 80, which conflicts with Laravel's development server or Docker port mapping.

**Solution**: Stop and disable Apache2

```bash
# Stop Apache2
sudo systemctl stop apache2

# Disable Apache2 from starting on boot
sudo systemctl disable apache2

# Verify it's stopped
sudo systemctl status apache2
```

After running these commands, refresh your browser and you should see the Commatix login page.

### Database Connection Issues

**Problem**: "Connection refused" or "Internal Server Error" when accessing the web app.

**Cause**: The `.env` file has incorrect `DB_HOST` setting for your environment.

**Solution**: Use the correct DB_HOST based on your environment:

```bash
# For Docker/Web Access (Laravel container → MySQL container)
DB_HOST=mysql

# For CLI/Artisan commands from your machine
DB_HOST=127.0.0.1 php artisan migrate
# OR permanently in .env (breaks web):
# DB_HOST=127.0.0.1
```

**Quick Fix**:
```bash
# If web app shows "Connection refused"
sed -i 's/^DB_HOST=127.0.0.1$/DB_HOST=mysql/' .env

# If artisan commands fail, prefix them:
DB_HOST=127.0.0.1 php artisan migrate
DB_HOST=127.0.0.1 php artisan db:seed
```

### DataGrip / Database GUI Connection

Connect to MySQL from DataGrip, TablePlus, HeidiSQL, or any database client.

#### Finding Your Connection Details

All database credentials are stored in your `.env` file. Here's how to find them:

```bash
# View your database configuration
cat .env | grep DB_
```

You should see:
```env
DB_CONNECTION=mysql
DB_HOST=mysql          # For Docker internal use only
DB_PORT=3306
DB_DATABASE=commatix
DB_USERNAME=sail
DB_PASSWORD=password
```

#### Connection Settings for External Tools

When connecting from **outside Docker** (like DataGrip on your Windows machine), use:

```
Host:     localhost (or 127.0.0.1)
Port:     3306 (from DB_PORT in .env)
Database: commatix (from DB_DATABASE in .env)
User:     sail (from DB_USERNAME in .env)
Password: password (from DB_PASSWORD in .env)
```

#### Important Notes

1. **Host Mapping**:
   - Use `localhost` or `127.0.0.1` for external tools (Windows, DataGrip, etc.)
   - The `DB_HOST=mysql` in `.env` is the Docker container name and only works inside the Docker network

2. **Port Forwarding**:
   - Laravel Sail automatically forwards MySQL port 3306 to your host machine
   - Verify with: `docker ps` and look for `0.0.0.0:3306->3306/tcp`

3. **Tenant Databases**:
   - Central database: `commatix` (main database)
   - Tenant databases: `tenant{uuid}` (created dynamically per tenant)
   - You'll see all databases once connected

#### Testing Your Connection

```bash
# Check if MySQL container is running
docker ps --filter "name=mysql"

# Test connection from command line
docker exec commatix-mysql-1 mysql -usail -ppassword -e "SHOW DATABASES;"

# Or use Laravel Sail
./vendor/bin/sail mysql
```

### Other Common Issues

**Port already in use**
```bash
# Check what's using port 80
sudo lsof -i :80

# Kill the process if needed
sudo kill -9 <PID>
```

---

## 🔐 Default Login Credentials

After running `php artisan db:seed`, use these credentials to access the application:

### Super Admin (Full System Access)
```
Email:    superadmin@commatix.io
Password: CommatixSA2025!
Access:   All tenants, system configuration, full admin panel
```

### Platform Admin
```
Email:    admin@commatix.io
Password: CommatixAdmin2025!
```

### Support Lead
```
Email:    support@commatix.io
Password: CommatixSupport2025!
```

### Demo Tenants

**TechStartup SA (Technology)**
```
Email:    john@techstartup.co.za
Password: TechDemo2025!
```

**Cape Finance (Financial Services)**
```
Email:    michael@capefinance.co.za
Password: FinanceDemo2025!
```

**Durban Health (Healthcare)**
```
Email:    priya@durbanhealth.co.za
Password: HealthDemo2025!
