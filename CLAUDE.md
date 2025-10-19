# Commatix - Project Context for Claude Code

## 🎯 Project Overview

**Commatix** is a modern, multi-tenant communication and workflow management platform built for South African SMEs.

### Tech Stack
- **Backend:** Laravel 12.x
- **Admin Panel:** Filament v4.0
- **Multi-Tenancy:** stancl/tenancy 3.5
- **Database:** MySQL (production), SQLite (development)
- **Cache/Queue:** Redis (production), Database (development)
- **Frontend:** Vite, TailwindCSS, Alpine.js
- **Package Manager:** pnpm (NOT npm)
- **Email:** Resend Laravel
- **SMS:** Vonage Client
- **Permissions:** Spatie Laravel Permission
- **Development Tools:** Laravel Telescope, Laravel Pulse, Laravel Pint, PHPStan, GrumPHP

### Key Features
1. **Multi-Tenant Architecture**
   - Tenant isolation with separate schemas
   - Tenant-aware Filament resources
   - Central + Tenant databases

2. **Workflow Management**
   - Workflow templates with milestones
   - Task scheduling and assignment
   - Approval workflows
   - Document management

3. **User Management**
   - Role-based access control (Super Admin, Tenant Admin, User)
   - Division-based organization
   - Approval groups
   - User type system

4. **Communication**
   - Email campaigns (Resend)
   - SMS campaigns (Vonage)
   - Subscriber management
   - Multi-channel notifications

5. **Subscription & Usage**
   - Tenant subscriptions
   - Usage tracking
   - Credit management (SMS, Email, etc.)
   - Top-up system

## 🏗️ Architecture

### Directory Structure
```
app/
├── Console/Commands/       # Artisan commands
├── Contracts/Services/     # Service interfaces (SOLID)
├── Filament/              # Filament admin panel
│   ├── Resources/         # CRUD resources
│   ├── Pages/             # Custom pages
│   └── Components/        # Reusable components
├── Models/                # Eloquent models
├── Policies/              # Authorization policies
├── Providers/             # Service providers
└── Services/              # Business logic services

database/
├── migrations/            # Database migrations
├── seeders/               # Database seeders
└── factories/             # Model factories

tests/
├── Feature/               # Feature tests
└── Unit/                  # Unit tests
```

### Service Layer (SOLID Architecture)

Commatix follows SOLID principles with interface-based design:

**Interfaces** (`app/Contracts/Services/`):
- `WorkflowRepositoryInterface`
- `TaskRepositoryInterface`
- `WorkflowLockingInterface`
- `TaskProgressionInterface`
- `AuthorizationServiceInterface`
- `CreditManagementInterface`

**Implementations** (`app/Services/`):
- Registered in `SolidServiceProvider`
- Injected via dependency injection
- Testable and mockable

### Multi-Tenancy

**Central Tables** (shared across all tenants):
- `tenants` - Tenant information
- `domains` - Tenant domains
- `user_types` - User type definitions
- `industries` - Industry categories

**Tenant Tables** (isolated per tenant):
- `users` - Tenant-specific users
- `divisions` - Organizational divisions
- `approval_groups` - Approval workflows
- `workflow_templates` - Workflow definitions
- `tasks` - Task instances
- `subscribers` - Communication subscribers
- `milestones` - Workflow milestones

**Tenant Context:**
```php
// Initialize tenant context
tenancy()->initialize($tenant);

// All queries now scoped to this tenant
$users = User::all(); // Only this tenant's users
```

## 🔐 Authentication & Authorization

### User Roles (Spatie Permissions)

**Super Admin** (`super_admin`):
- Access to ALL tenants and data
- System-wide configuration
- Tenant management
- Can impersonate any user
- Access to debugging tools

**Tenant Admin** (`tenant_admin`):
- Full access within their tenant
- User management
- Division and approval group management
- Workflow template creation
- Subscription and billing

**Tenant User** (`tenant_user`):
- Task completion
- View assigned workflows
- Submit for approval
- Limited resource access

### Filament Authorization

Resources use policies:
```php
public static function canViewAny(): bool
{
    return auth()->user()->can('view_any_tenant');
}
```

## 📊 Database Schema Highlights

### Tenants
- Subscription management
- Usage tracking
- Credit balances (email, SMS, storage)

### Workflows
- Template-based design
- Industry-specific templates (Healthcare, Legal, Finance, etc.)
- Milestones with approval requirements
- Document attachments
- Status tracking

### Tasks
- Assigned to users or divisions
- Priority levels (low, medium, high, urgent)
- Status tracking
- Due dates and scheduling
- Progress tracking

### Subscribers & Lists
- Segmented subscriber lists
- Multi-channel contact information
- Opt-in/opt-out management
- Campaign targeting

## 🧪 Testing Strategy

### Test Coverage
- **Unit Tests:** Service classes, models, utilities
- **Feature Tests:** API endpoints, workflows, multi-tenancy

### Key Test Areas
1. **Tenant Isolation:** Ensure data doesn't leak between tenants
2. **Permissions:** Verify role-based access control
3. **Workflows:** Test milestone progression
4. **Task Assignment:** Test assignment strategies
5. **Approval Flows:** Test approval group workflows

### Running Tests
```bash
composer test              # Run all tests
php artisan test --filter=WorkflowTest
```

## 🎨 Code Style & Quality

### Standards
- **PSR-12** - PHP Standards Recommendation
- **PHPStan Level 8** - Strict static analysis
- **Type Hints** - All methods have return types and parameter types

### Quality Tools
```bash
composer lint          # Run all quality checks
composer lint:fix      # Auto-fix issues
composer grumphp       # Pre-commit hooks
```

### Pre-Commit Checks (GrumPHP)
- PHPStan static analysis
- PHP_CodeSniffer (PSR-12)
- PHPCPD (copy-paste detection)
- Composer validation
- PHPUnit tests

## 🎨 UI/UX Design System

Commatix follows a comprehensive **Design System** for consistent, accessible, and elegant user interfaces.

### Design Philosophy
- **Glassmorphism aesthetic** - Modern, layered glass effects
- **South African UX standards** - Right-aligned form buttons, local formats
- **WCAG 2.1 AA compliant** - Accessible to all users
- **Mobile-first responsive** - Optimized for all screen sizes
- **Performance-focused** - Fast, smooth, optimized

### Color System (OKLCH)
```
commatix-500: oklch(0.65 0.18 200)  - Primary brand color
sa-gold-500:  oklch(0.8 0.12 85)    - South African accent
tenant-blue:  oklch(0.65 0.18 230)  - Tenant-specific colors
```

### Typography
- **Font Family:** Figtree sans-serif
- **Type Scale:** text-xs to text-3xl (12px to 30px)
- **Font Weights:** font-medium (buttons), font-semibold (subheadings), font-bold (headings)

### Animations
- `animate-fade-in` - Entrance animations (0.3s)
- `animate-slide-up` - Card reveals (0.3s)
- `animate-glass-float` - Decorative floating (6s infinite)
- `animate-metric-up` - Data visualization (0.6s)

### South African UX Standards
- Form action buttons: **ALWAYS right-aligned**
- Button order: Cancel (left) → Primary Action (right)
- Date format: DD/MM/YYYY
- Currency: R 1,250.00 (with space after R)
- Phone: +27 12 345 6789 (with spaces)

### UI/UX Commands
```bash
/design-system        # Quick reference
/ui-check [file]      # Validate UI implementation
/ui-expert <task>     # UI/UX specialist with browser preview
```

### Documentation
- **Complete Guide:** `DESIGN_SYSTEM.md`
- **Theme Config:** `tailwind.config.js`
- **Components:** `resources/views/filament/components/`

## 📦 Key Packages

### Laravel Packages
- `laravel/framework:^12.0` - Core framework
- `filament/filament:^4.0` - Admin panel
- `stancl/tenancy:^3.5.1` - Multi-tenancy
- `spatie/laravel-permission:^6.0` - Roles & permissions
- `laravel/telescope:^5.14` - Debugging
- `laravel/pulse:^1.4` - Performance monitoring

### Communication
- `resend/resend-laravel:^0.19` - Email service
- `vonage/client:^4.0` - SMS service

### Development Tools
- `laravel/pint:^1.22` - Code formatting
- `phpstan/phpstan:^2.1` - Static analysis
- `phpro/grumphp:^2.13` - Git hooks
- `laravel-shift/blueprint:^2.12` - Code generation

## 🔄 Development Workflow

### Daily Development
```bash
# Start development environment
composer dev  # Starts Laravel, Queue, Vite (uses pnpm)

# Or individually:
php artisan serve              # Laravel on :8000
php artisan queue:work         # Queue worker
pnpm run dev                   # Vite HMR
```

### Creating Features

1. **Design Schema** (use Blueprint YAML)
2. **Generate Code** (`php artisan blueprint:build`)
3. **Create Filament Resource** (`/filament-resource ModelName`)
4. **Implement Business Logic** (Service classes)
5. **Design UI/UX** (follow `DESIGN_SYSTEM.md`)
6. **Validate UI** (`/ui-check [file-path]`)
7. **Write Tests** (Unit + Feature)
8. **Run Quality Checks** (`composer lint`)
9. **Commit** (GrumPHP runs automatically)

### Database Workflow
```bash
# Create migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Fresh start (dev only!)
php artisan migrate:fresh --seed
```

## 🖥️ IDE Setup (WSL + PhpStorm)

### PhpStorm Configuration for WSL Projects

This project runs on WSL (Windows Subsystem for Linux), but can be accessed from PhpStorm on Windows.

#### Terminal Setup

To use Claude Code and other CLI tools directly in PhpStorm's terminal:

1. Open PhpStorm Settings: `File → Settings` (or `Ctrl+Alt+S`)
2. Navigate to: `Tools → Terminal`
3. Change "Shell path" to:
   ```
   C:\Windows\System32\wsl.exe
   ```
4. Apply and restart any open terminal tabs

Now your PhpStorm terminal will run in WSL, giving you access to:
- `claude` - Claude Code CLI
- `php artisan` - Laravel commands
- `composer` - Dependency management
- `npm` - Node package management

#### Why This Is Needed

PhpStorm on Windows opens WSL projects using UNC paths (`\\wsl.localhost\...`), which Windows CMD doesn't support properly. By switching to WSL terminal, you get:
- ✅ Native Linux environment
- ✅ Direct access to all CLI tools
- ✅ No path translation issues
- ✅ Proper file watching and hot reload

#### Alternative: Direct WSL Access

You can also run `claude` directly from a WSL Ubuntu terminal:
```bash
cd ~/projects/commatix
claude
```

This gives you a separate Claude Code instance outside PhpStorm.

## 🌍 Multi-Tenancy Patterns

### Creating Tenant-Aware Resources

```php
// In Filament Resource
protected static ?string $tenantRelationshipName = 'tenant';

public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('tenant_id', auth()->user()->tenant_id);
}
```

### Tenant-Aware Models

```php
use Stancl\Tenancy\Database\Concerns\BelongToTenant;

class WorkflowTemplate extends Model
{
    use BelongToTenant;

    // Automatically scoped to current tenant
}
```

### Switching Tenants (Super Admin)

```php
$tenant = Tenant::find(1);
tenancy()->initialize($tenant);

// All queries now scoped to $tenant
```

## 🚀 Deployment Considerations

### Environment Setup
- Set `APP_ENV=production`
- Configure Redis for cache and queues
- Set up queue workers (supervisor)
- Configure email (Resend)
- Configure SMS (Vonage)
- Enable caching (`config:cache`, `route:cache`)

### Pre-Deployment Checklist
Run `/deploy-check` command to verify:
- ✅ All tests passing
- ✅ Code quality checks pass
- ✅ Dependencies up to date
- ✅ Migrations ready
- ✅ Configuration cached
- ✅ No sensitive data in repo

## 🎓 Learning Resources

### For New Developers

**Must Read:**
1. Laravel 12 Documentation - https://laravel.com/docs/12.x
2. Filament 4 Documentation - https://filamentphp.com/docs/4.x
3. Stancl Tenancy - https://tenancyforlaravel.com/docs/v3
4. Spatie Permissions - https://spatie.be/docs/laravel-permission

**Code Patterns to Study:**
- `app/Services/WorkflowService.php` - Service pattern
- `app/Filament/Resources/UserResource.php` - Filament resources
- `tests/Unit/WorkflowTemplateTest.php` - Testing patterns

### Common Gotchas

1. **Tenant Context:** Always ensure tenant is initialized before tenant-scoped queries
2. **Mass Assignment:** Add fields to `$fillable` or `$guarded`
3. **N+1 Queries:** Use eager loading (`->with()`)
4. **Cache:** Clear cache after config changes
5. **Queue:** Long operations should be queued

## 🔧 Useful Artisan Commands

```bash
# Tenant management
php artisan tenants:list
php artisan tenants:migrate

# Cache management
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Queue management
php artisan queue:work
php artisan queue:failed
php artisan queue:retry all

# Filament
php artisan make:filament-resource ModelName --generate
php artisan filament:upgrade

# Development
php artisan telescope:install
php artisan pulse:check
```

## 🐛 Debugging

### Laravel Telescope
Access at `/telescope` (local only)
- HTTP requests
- Database queries
- Queue jobs
- Cache operations
- Logs and exceptions

### Laravel Pulse
Access at `/pulse` (requires setup)
- Real-time performance monitoring
- Server metrics
- Job throughput

### Tinker
```bash
php artisan tinker

# Example: Test tenant isolation
$tenant = Tenant::first();
tenancy()->initialize($tenant);
User::count();
```

## 📝 Important Notes

### For Claude Code

**When implementing features:**
1. Check existing patterns first
2. Follow SOLID principles
3. Follow Design System (`DESIGN_SYSTEM.md`)
4. Write tests
5. Consider multi-tenancy
6. Add proper type hints
7. Document complex logic
8. Validate UI/UX with `/ui-check`

**Preferred patterns:**
- Service classes for business logic
- Repository pattern for data access
- Jobs for async operations
- Events for decoupling
- Policies for authorization

**Avoid:**
- Logic in controllers (keep thin)
- Direct model queries in views
- Hardcoded values
- Missing type hints
- Untested code

---

**Project Status:** Active Development
**Version:** 1.0 (Pre-launch)
**Target Launch:** Q2 2025
**Team Size:** Small team (2-5 developers)

Use `/help` to see all available Claude Code commands specific to this project.
