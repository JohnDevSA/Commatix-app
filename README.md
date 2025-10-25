# Commatix

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Laravel](https://img.shields.io/badge/laravel-12.x-red)
![Filament](https://img.shields.io/badge/admin-filament%204.x-orange)
![PHP](https://img.shields.io/badge/php-^8.2-blue)
![Tenancy](https://img.shields.io/badge/multi--tenant-stancl%203.5-green)

> **Commatix** is a modern, multi-tenant communication platform built with Laravel and enhanced with Filament Admin, queue workers, and dynamic workflow handling.

---

## 🧱 Stack Overview

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament v4
- **Multi-Tenancy**: [stancl/tenancy](https://github.com/stancl/tenancy)
- **Role & Permission Management**: [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
- **Email Integration**: [Resend Laravel](https://github.com/resend/resend-laravel)
- **SMS Integration**: [Vonage Client](https://github.com/Vonage/vonage-php-sdk-core)
- **Scaffolding & Developer Tools**:
    - Laravel Blueprint
    - Laravel Telescope (debugging)
    - Laravel Pulse (performance monitoring)
    - Laravel Pint (code formatting)
    - Laravel Pail (log streaming)
    - Larastan (static analysis)
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
   - Telescope (debugging): http://localhost/telescope
   - Pulse (performance): http://localhost/pulse
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

## ☸️ Kubernetes Queue Workers (Optional)

Commatix supports running queue workers in Kubernetes for production-like scaling and management while keeping the development environment (Sail) running locally.

### Architecture

**Hybrid Setup:**
- **Sail (Docker Compose)**: Laravel app, MySQL, Mailpit, Redis
- **Kubernetes**: Queue workers (default + campaigns)

This allows you to:
- ✅ Learn Kubernetes without disrupting development
- ✅ Scale queue workers independently (1-20+ pods)
- ✅ Monitor workers with Kubernetes tools
- ✅ Practice production deployment patterns

### Prerequisites

- Docker Desktop with Kubernetes enabled
- kubectl configured to access Docker Desktop cluster
- Sail already running (`./vendor/bin/sail up -d`)

### Quick Start

```bash
# 1. Ensure Sail is running (required for MySQL & Redis)
./vendor/bin/sail up -d

# 2. Create Kubernetes namespace and secrets
kubectl create namespace commatix

# Extract APP_KEY and DB_PASSWORD from your .env file
kubectl create secret generic commatix-app \
  --from-literal=app-key="$(grep APP_KEY .env | cut -d '=' -f2)" \
  -n commatix

kubectl create secret generic commatix-db \
  --from-literal=password="$(grep DB_PASSWORD .env | cut -d '=' -f2)" \
  -n commatix

# 3. Build queue worker Docker image
docker build -f Dockerfile.k8s-sail -t commatix-queue:latest .

# 4. Deploy queue workers
kubectl apply -f k8s/queue-workers-deployment.yaml

# 5. Verify workers are running
kubectl get pods -n commatix
kubectl logs -f deployment/commatix-queue-default -n commatix
```

### Monitoring & Management

```bash
# Check all resources
kubectl get all -n commatix

# Scale workers up/down
kubectl scale deployment/commatix-queue-default --replicas=5 -n commatix
kubectl scale deployment/commatix-queue-campaigns --replicas=10 -n commatix

# View logs
kubectl logs -f deployment/commatix-queue-default -n commatix
kubectl logs -f -l app=queue-worker -n commatix --max-log-requests=10

# Restart workers (after code changes)
docker build -f Dockerfile.k8s-sail -t commatix-queue:latest .
kubectl rollout restart deployment/commatix-queue-default -n commatix
kubectl rollout restart deployment/commatix-queue-campaigns -n commatix
```

### Documentation

- **[k8s/README.md](k8s/README.md)** - Complete operations guide
- **[k8s/QUICK-START.md](k8s/QUICK-START.md)** - Command reference
- **[k8s/TROUBLESHOOTING.md](k8s/TROUBLESHOOTING.md)** - Common issues & solutions
- **[k8s/SETUP-SUMMARY.md](k8s/SETUP-SUMMARY.md)** - Initial setup walkthrough

### Important Notes

1. **Sail must be running** - Workers need MySQL and Redis from Sail
2. **Rebuild after code changes** - Workers use a Docker image that needs rebuilding
3. **Temporary image** - Using Sail-based image (3GB), should be optimized for production

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
```

---

## 🐛 Developer & Debugging Tools

Commatix includes powerful debugging and monitoring tools to help developers track application behavior, performance, and errors.

### Available Tools

#### ✅ Laravel Telescope
**Status:** Fully Configured & Available

Laravel Telescope provides elegant debugging for Laravel applications, tracking:
- HTTP requests and responses
- Database queries and performance
- Jobs and queue monitoring  
- Cache operations
- Mail previews
- Exception tracking
- Log entries

**Access:** http://localhost/telescope

**Authorization:**
- Automatically enabled in `local` environment
- Super Admin users in production environments

**Features:**
- Request/response debugging with timing
- SQL query profiler with explain plans
- Job queue monitoring
- Real-time exception tracking
- Email preview (never send test emails again!)

#### ✅ Laravel Pulse
**Status:** Fully Configured & Available

Laravel Pulse offers real-time application performance monitoring:
- Server resource usage (CPU, memory, disk)
- Slow database queries
- Slow HTTP requests
- Failed jobs and exceptions
- Cache hit/miss rates
- User activity tracking

**Access:** http://localhost/pulse

**Authorization:**
- Automatically enabled in `local` environment
- Super Admin users in production environments

**Features:**
- Live performance metrics dashboard
- Historical trends and patterns
- Slow query identification
- Exception monitoring
- Job throughput tracking

#### ⏳ Laravel Horizon
**Status:** Planned - Currently Unavailable

**Why not available?**
Laravel Horizon has a dependency conflict with the current Vonage SMS client (v4.x) when running on PHP 8.4. The conflict is in the `lcobucci/jwt` library used by both packages.

**Timeline:** We're actively monitoring package updates and will integrate Horizon once the dependency conflict is resolved. Expected in Q1 2025.

**Alternative:** Use **Laravel Telescope** to monitor queue jobs in the meantime. It provides comprehensive job tracking, though without Horizon's advanced queue management UI.

**What Horizon will provide (when available):**
- Advanced queue dashboard
- Job retry management
- Queue metrics and statistics
- Failed job management
- Real-time job throughput

### Using the Debugger Tools

**During Development:**
```bash
# Access Telescope dashboard
open http://localhost/telescope

# Access Pulse dashboard
open http://localhost/pulse

# View recent exceptions
# Telescope → Exceptions tab

# Find slow queries
# Pulse → Slow Queries card

# Check queue jobs
# Telescope → Jobs tab
```

**Clearing Debug Data:**
```bash
# Clear Telescope entries
docker compose exec laravel.test php artisan telescope:clear

# Telescope retains data for 24 hours by default
# Configure retention in config/telescope.php

# Pulse data retention is configured in config/pulse.php
# Default: 7 days
```

**Production Considerations:**
- Telescope and Pulse are enabled but restricted to Super Admin users only
- Consider disabling Telescope in production (`TELESCOPE_ENABLED=false` in `.env`)
- Pulse is lightweight and safe for production use
- Both tools use database storage for collected metrics

### Troubleshooting Debugger Tools

**Issue:** Cannot access /telescope or /pulse

**Solution:**
1. Ensure you're logged in as Super Admin or in local environment
2. Clear config cache: `docker compose exec laravel.test php artisan config:clear`
3. Check authorization gates in:
   - `app/Providers/TelescopeServiceProvider.php`
   - `app/Providers/AppServiceProvider.php`

**Issue:** Telescope not recording data

**Solution:**
1. Check `TELESCOPE_ENABLED=true` in `.env`
2. Verify migrations ran: `docker compose exec laravel.test php artisan migrate:status | grep telescope`
3. Clear config: `docker compose exec laravel.test php artisan config:clear`

---

## 📚 Additional Documentation

- [CLAUDE.md](./CLAUDE.md) - AI assistant context and development guidelines
- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [stancl/tenancy Documentation](https://tenancyforlaravel.com/docs)

---

## 📄 License

MIT License. See [LICENSE](./LICENSE) for details.
