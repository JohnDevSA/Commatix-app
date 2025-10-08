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

### Prerequisites

- PHP 8.2 or later
- Composer
- Node.js & NPM
- SQLite or MySQL
- Redis (for queues)
- Git

### Installation

```bash
git clone https://github.com/your-org/commatix.git
cd commatix

composer install
cp .env.example .env
php artisan key:generate

npm install
npm run dev
