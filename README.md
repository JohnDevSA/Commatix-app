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

### Other Common Issues

**Port already in use**
```bash
# Check what's using port 80
sudo lsof -i :80

# Kill the process if needed
sudo kill -9 <PID>
