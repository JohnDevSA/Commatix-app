# WSL Development Environment Setup

## Overview

Commatix is developed on **Windows Subsystem for Linux (WSL)** using **Docker Desktop** and **Laravel Sail**. This guide explains the setup and common gotchas.

## Architecture

```
Windows Host (PhpStorm IDE)
    ↓
WSL 2 (Ubuntu)
    ↓
Docker Desktop (Docker Engine)
    ↓
Laravel Sail Containers
    ├── commatix-laravel.test-1 (PHP 8.3, Laravel 12)
    ├── commatix-mysql-1 (MySQL 8.0)
    ├── commatix-redis-1 (Redis 7.x)
    └── commatix-mailpit-1 (Email testing)
```

## Prerequisites

### 1. Install WSL 2

```powershell
# Run in PowerShell as Administrator
wsl --install -d Ubuntu
```

### 2. Install Docker Desktop

- Download from https://www.docker.com/products/docker-desktop
- Enable **WSL 2 backend** in Docker Desktop settings
- Enable **Kubernetes** (optional, for K8s development)

### 3. PhpStorm Configuration

**Terminal Setup** (to run commands in WSL):

1. Open PhpStorm Settings: `File → Settings` (`Ctrl+Alt+S`)
2. Navigate to: `Tools → Terminal`
3. Change "Shell path" to:
   ```
   C:\Windows\System32\wsl.exe
   ```
4. Apply and restart any open terminal tabs

Now your PhpStorm terminal runs in WSL, giving you access to:
- `claude` - Claude Code CLI
- `php artisan` - Laravel commands
- `composer` - Dependency management
- `docker` / `./vendor/bin/sail` - Docker commands

## Project Setup

### Initial Setup

```bash
# Clone repository (from WSL terminal)
cd ~/projects
git clone <repo-url> commatix
cd commatix

# Start Docker containers
./vendor/bin/sail up -d

# Install dependencies
./vendor/bin/sail composer install
./vendor/bin/sail pnpm install

# Setup environment
cp .env.example .env
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate --seed

# Configure Git hooks (IMPORTANT!)
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

### Git Hooks Setup

Commatix uses **custom Git hooks** stored in `.githooks/` (tracked in version control) instead of `.git/hooks/` (not tracked).

**Why this matters:**
- `.git/hooks/` - Not tracked by Git, each developer must set up manually
- `.githooks/` - Tracked by Git, everyone gets the same hooks automatically

**Setup (one-time per developer):**

```bash
# Configure Git to use .githooks directory
git config core.hooksPath .githooks

# Ensure hooks are executable
chmod +x .githooks/pre-commit
```

**What the pre-commit hook does:**

1. **Pint** - Laravel code style fixer (PSR-12)
2. **PHPCS** - PHP Code Sniffer (PSR-12 compliance)
3. **GrumPHP** - Runs PHPStan, PHPCPD, PHPUnit, Composer validation

All checks run **inside Docker** via `docker exec commatix-laravel.test-1`, ensuring consistency across environments.

## Running Commands

### Always Use Docker!

Since Commatix runs in Docker, **all PHP/Composer/Artisan commands must run inside the container**.

### Command Patterns

**Option 1: Using `docker exec` (direct)**

```bash
docker exec commatix-laravel.test-1 php artisan migrate
docker exec commatix-laravel.test-1 composer test
docker exec commatix-laravel.test-1 ./vendor/bin/pint
docker exec commatix-laravel.test-1 ./vendor/bin/phpstan analyse
```

**Option 2: Using Laravel Sail (wrapper)**

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail composer test
./vendor/bin/sail pnpm dev
```

**Option 3: Create an alias (recommended)**

Add to your `~/.bashrc` or `~/.zshrc`:

```bash
alias sail='./vendor/bin/sail'
```

Then:

```bash
sail artisan migrate
sail composer test
sail pnpm dev
```

## Common Tasks

### Starting Development Environment

```bash
# Start all containers
./vendor/bin/sail up -d

# Or with logs
./vendor/bin/sail up
```

### Running Tests

```bash
# All tests
docker exec commatix-laravel.test-1 php artisan test

# Specific test
docker exec commatix-laravel.test-1 php artisan test --filter=WorkflowTest

# Using Sail
./vendor/bin/sail artisan test
```

### Code Quality Checks

```bash
# Run all quality checks
docker exec commatix-laravel.test-1 composer lint

# Individual checks
docker exec commatix-laravel.test-1 ./vendor/bin/pint --test    # Check only
docker exec commatix-laravel.test-1 ./vendor/bin/pint           # Auto-fix
docker exec commatix-laravel.test-1 ./vendor/bin/phpstan analyse
docker exec commatix-laravel.test-1 ./vendor/bin/grumphp run
```

### Database Operations

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Fresh start with seed data
./vendor/bin/sail artisan migrate:fresh --seed

# Create migration
./vendor/bin/sail artisan make:migration create_table_name

# Seed database
./vendor/bin/sail artisan db:seed
```

### Cache Management

```bash
# Clear all caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

# Or use command
/cache-clear
```

### Frontend Development

```bash
# Install packages (use pnpm, NOT npm!)
./vendor/bin/sail pnpm install

# Development server (hot reload)
./vendor/bin/sail pnpm dev

# Production build
./vendor/bin/sail pnpm build
```

## Troubleshooting

### Issue: Git hooks using `.bat` files

**Symptom:**
```
vendor/bin/grumphp.bat: 1: @ECHO: not found
vendor/bin/grumphp.bat: 2: setlocal: not found
Could not open input file: %BIN_TARGET%
```

**Cause:** Git is trying to execute Windows batch files in WSL.

**Solution:**
```bash
# Configure Git to use .githooks directory
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit

# Verify configuration
git config core.hooksPath  # Should output: .githooks
```

### Issue: Permission denied errors

**Symptom:**
```
bash: ./vendor/bin/sail: Permission denied
```

**Solution:**
```bash
chmod +x ./vendor/bin/sail
```

### Issue: Docker containers not starting

**Check Docker Desktop:**
1. Ensure Docker Desktop is running
2. Check WSL 2 integration: `Settings → Resources → WSL Integration`
3. Ensure your WSL distro is enabled

**Check containers:**
```bash
docker ps -a
./vendor/bin/sail ps
```

**Restart containers:**
```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### Issue: Port already in use (80, 3306, 6379)

**Solution 1: Stop conflicting services**
```bash
# Find what's using port 80
sudo lsof -i :80

# Stop Apache/Nginx if running
sudo service apache2 stop
sudo service nginx stop
```

**Solution 2: Change Laravel Sail ports**

Edit `docker-compose.yml` or use environment variables:
```bash
# In .env
APP_PORT=8080
FORWARD_DB_PORT=33060
FORWARD_REDIS_PORT=63790
```

### Issue: PHPStorm can't find PHP executable

**Symptom:** PHPStorm shows "PHP interpreter not configured"

**Solution:** Configure Docker-based PHP interpreter

1. `File → Settings → PHP`
2. Click `...` next to CLI Interpreter
3. Add new interpreter → `From Docker, Vagrant, VM, WSL, Remote...`
4. Select `Docker Compose`
5. Configuration files: `docker-compose.yml`
6. Service: `laravel.test`

### Issue: File watchers not working (Vite hot reload)

**Symptom:** Changes to frontend files don't trigger reload

**Solution:** Increase inotify watchers

```bash
# In WSL terminal
echo fs.inotify.max_user_watches=524288 | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

## Performance Optimization

### WSL 2 Performance Tips

**1. Store project files in WSL filesystem** (not `/mnt/c/`)
```bash
# Good: ~/projects/commatix
# Bad:  /mnt/c/Users/YourName/projects/commatix
```

**2. Limit Docker memory usage**

Create `~/.wslconfig` (on Windows side):
```ini
[wsl2]
memory=8GB
processors=4
swap=2GB
```

**3. Use native Docker volumes for dependencies**

Laravel Sail already does this for `vendor/` and `node_modules/`.

## Environment Files

### Development (.env)

```bash
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=commatix
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Access Points

- **Application:** http://localhost
- **Mailpit (Email testing):** http://localhost:8025
- **MySQL:** localhost:3306 (from host)
- **Redis:** localhost:6379 (from host)

## Best Practices

### 1. Always run commands in Docker

```bash
# ❌ Don't do this
php artisan migrate

# ✅ Do this instead
docker exec commatix-laravel.test-1 php artisan migrate
# or
./vendor/bin/sail artisan migrate
```

### 2. Use pnpm, not npm

```bash
# ❌ Don't
./vendor/bin/sail npm install

# ✅ Do
./vendor/bin/sail pnpm install
```

### 3. Keep Docker containers running

Leave containers running during development:
```bash
./vendor/bin/sail up -d
```

Stop when finished for the day:
```bash
./vendor/bin/sail down
```

### 4. Configure Git hooks immediately

After cloning:
```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

### 5. Use PhpStorm WSL terminal

- Configure once: `Tools → Terminal → Shell path: C:\Windows\System32\wsl.exe`
- Access all WSL tools directly from PhpStorm
- No need to switch to separate WSL terminal

## Additional Resources

- **Laravel Sail Documentation:** https://laravel.com/docs/12.x/sail
- **Docker Desktop WSL 2 Backend:** https://docs.docker.com/desktop/wsl/
- **WSL Documentation:** https://learn.microsoft.com/en-us/windows/wsl/

## Quick Reference

### Daily Workflow

```bash
# Morning: Start containers
./vendor/bin/sail up -d

# Work on code...

# Before commit (hooks run automatically)
git add .
git commit -m "Your message"

# Evening: Stop containers
./vendor/bin/sail down
```

### Quality Checks (CI/CD)

```bash
# Run all checks
docker exec commatix-laravel.test-1 composer lint

# Individual checks
docker exec commatix-laravel.test-1 ./vendor/bin/pint --test
docker exec commatix-laravel.test-1 ./vendor/bin/phpstan analyse
docker exec commatix-laravel.test-1 php artisan test
```

### Debugging

```bash
# View logs
./vendor/bin/sail logs

# Follow logs
./vendor/bin/sail logs -f

# Access container shell
docker exec -it commatix-laravel.test-1 bash

# Laravel Telescope (debugging UI)
# Visit: http://localhost/telescope
```

---

**Last Updated:** 2025-10-25
**Maintained By:** Commatix Development Team
