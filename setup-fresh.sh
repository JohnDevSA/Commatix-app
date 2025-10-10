#!/bin/bash

# Commatix Fresh Development Environment Setup
# For Ubuntu/WSL2 with pnpm
# Author: Commatix Team
# Last Updated: 2025

set -e  # Exit on error

echo "🚀 Commatix Fresh Setup - Starting..."
echo "================================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running in WSL
if grep -qEi "(Microsoft|WSL)" /proc/version &> /dev/null ; then
    echo -e "${GREEN}✓${NC} Running in WSL2"
else
    echo -e "${YELLOW}⚠${NC} Not running in WSL2, continuing anyway..."
fi

# Function to print success
success() {
    echo -e "${GREEN}✓${NC} $1"
}

# Function to print error
error() {
    echo -e "${RED}✗${NC} $1"
}

# Function to print info
info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

# ============================================
# 1. System Update
# ============================================
echo ""
echo "📦 Step 1: Updating system packages..."
sudo apt update && sudo apt upgrade -y
success "System updated"

# ============================================
# 2. Install Essential Tools
# ============================================
echo ""
echo "🔧 Step 2: Installing essential tools..."
sudo apt install -y git curl wget build-essential software-properties-common unzip
success "Essential tools installed"

# ============================================
# 3. Fix DNS (WSL2 Issue)
# ============================================
echo ""
echo "🌐 Step 3: Fixing DNS configuration..."
sudo rm -f /etc/resolv.conf
echo "nameserver 8.8.8.8" | sudo tee /etc/resolv.conf
echo "nameserver 8.8.4.4" | sudo tee -a /etc/resolv.conf
sudo chattr +i /etc/resolv.conf

# Configure WSL to not auto-generate resolv.conf
if [ ! -f /etc/wsl.conf ]; then
    sudo bash -c 'cat > /etc/wsl.conf << EOF
[network]
generateResolvConf = false
EOF'
    success "WSL DNS configured"
else
    info "WSL config already exists"
fi

# Test DNS
if ping -c 1 google.com &> /dev/null; then
    success "DNS working correctly"
else
    error "DNS not working - please restart WSL with: wsl --shutdown"
    exit 1
fi

# ============================================
# 4. Install PHP 8.2+
# ============================================
echo ""
echo "🐘 Step 4: Installing PHP 8.2..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y \
    php8.2 \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-redis \
    php8.2-intl \
    php8.2-dom \
    php8.2-fileinfo \
    php8.2-fpm \
    php8.2-pdo \
    php8.2-soap \
    php8.2-tokenizer \
    php8.2-xmlwriter

php --version
success "PHP 8.2 installed"

# ============================================
# 5. Install Composer
# ============================================
echo ""
echo "🎼 Step 5: Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    success "Composer installed"
else
    info "Composer already installed"
fi
composer --version

# ============================================
# 6. Install NVM and Node.js
# ============================================
echo ""
echo "📦 Step 6: Installing NVM and Node.js..."

# Install NVM if not present
if [ ! -d "$HOME/.nvm" ]; then
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
    success "NVM installed"
else
    info "NVM already installed"
fi

# Load NVM
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Install Node.js
nvm install 22.14.0
nvm use 22.14.0
nvm alias default 22.14.0

node --version
npm --version
success "Node.js 22.14.0 installed"

# ============================================
# 7. Install pnpm
# ============================================
echo ""
echo "📦 Step 7: Installing pnpm..."
npm install -g pnpm
pnpm --version
success "pnpm installed"

# Configure pnpm
pnpm config set store-dir ~/.pnpm-store
pnpm config set cache-dir ~/.pnpm-cache
success "pnpm configured"

# ============================================
# 8. Install Claude Code
# ============================================
echo ""
echo "🤖 Step 8: Installing Claude Code..."
pnpm add -g @anthropic-ai/claude-code
claude-code --version
success "Claude Code installed"

info "Don't forget to set your API key:"
info "  export ANTHROPIC_API_KEY='your-key-here'"
info "  Or run: claude-code auth login"

# ============================================
# 9. Install Docker Desktop (Check)
# ============================================
echo ""
echo "🐳 Step 9: Checking Docker..."
if command -v docker &> /dev/null; then
    docker --version
    success "Docker is installed"
else
    error "Docker not found!"
    info "Please install Docker Desktop for Windows with WSL2 integration"
    info "Download from: https://www.docker.com/products/docker-desktop"
fi

# ============================================
# 10. Setup Commatix Project
# ============================================
echo ""
echo "⚙️  Step 10: Setting up Commatix project..."

# Install Composer dependencies
if [ -f "composer.json" ]; then
    info "Installing PHP dependencies..."
    composer install
    success "Composer dependencies installed"
else
    error "composer.json not found - are you in the Commatix root?"
fi

# Install pnpm dependencies
if [ -f "package.json" ]; then
    info "Installing Node dependencies..."
    pnpm install
    success "pnpm dependencies installed"
else
    error "package.json not found"
fi

# Setup environment file
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        success "Created .env file"
        
        # Generate app key
        php artisan key:generate
        success "Generated application key"
    else
        error ".env.example not found"
    fi
else
    info ".env already exists"
fi

# ============================================
# 11. Initialize Claude Code
# ============================================
echo ""
echo "🤖 Step 11: Initializing Claude Code for Commatix..."

if [ ! -d ".claude" ]; then
    claude-code init
    
    # Create Commatix-specific instructions
    cat > .claude/instructions.md << 'EOF'
# Commatix Project Context

## Tech Stack
- **Backend**: Laravel 12.x with PHP 8.2+
- **Frontend**: Vite 6.0 + Tailwind CSS 3.4 + Alpine.js
- **Database**: MySQL with multi-tenant (stancl/tenancy v3.5)
- **Admin**: Filament v3
- **Cache/Queue**: Redis
- **Package Manager**: pnpm (NOT npm)

## Architecture
- Multi-tenant SaaS (database-per-tenant)
- Workflow management with milestones and tasks
- Campaign management (Email/SMS/WhatsApp)
- Document management with POPIA compliance
- South African market focus (PayFast, POPIA, SARS)

## Development Commands
```bash
# Start all services (Laravel Sail)
composer dev
# or
./vendor/bin/sail up -d

# Frontend dev server (use pnpm!)
pnpm dev

# Production build
pnpm build

# Testing
php artisan test

# Code quality
composer lint
composer lint:fix

# Database
php artisan migrate
php artisan db:seed
