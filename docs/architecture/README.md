# Commatix Architecture Documentation

Welcome to the Commatix architecture documentation. This guide will help you understand the system's structure, patterns, and best practices to ensure consistent, maintainable development.

## 📚 Documentation Index

### 1. [Multi-Tenant Architecture Guidelines](MULTI_TENANT_GUIDELINES.md)
**Critical for all development** - Learn how to properly implement tenant isolation, role-based access control, and data integrity.

**Topics covered:**
- Core multi-tenant principles
- Model configuration checklist
- Filament resource patterns
- Service layer tenant validation
- Common pitfalls to avoid
- Testing checklist
- Database migration patterns

**Read this first** if you're working on any tenant-specific feature.

### 2. [Filament Resource Patterns](FILAMENT_PATTERNS.md)
**Essential for UI development** - Comprehensive guide to building Filament resources, forms, tables, and widgets.

**Topics covered:**
- Resource structure and configuration
- Access control implementation
- Query scoping patterns
- Form field patterns (text, select, toggle, etc.)
- Table column patterns (badges, counts, dates)
- Filter patterns
- Action patterns (standard and custom)
- Widget patterns (stats and charts)
- Notification patterns
- Styling conventions

**Use this** when building any Filament resource or widget.

### 3. [Service Layer Guidelines](SERVICE_LAYER.md)
**Critical for business logic** - Learn how to properly structure services, ensure tenant isolation, and maintain clean architecture.

**Topics covered:**
- When to create a service
- Service structure and patterns
- Interface-based development
- Real-world examples (TaskSchedulingService, CreditManagementService)
- Tenant validation patterns
- Database transaction usage
- Exception handling
- Dependency injection
- Testing services

**Use this** when implementing complex business logic or coordinating multiple models.

### 4. [Testing Guidelines](TESTING_GUIDELINES.md)
**Essential for quality assurance** - Comprehensive testing strategies for multi-tenant applications.

**Topics covered:**
- Multi-tenant testing principles
- Unit test patterns (models, services, scopes)
- Feature test patterns (resources, APIs)
- Testing factories
- Testing checklist for every feature
- CI/CD integration
- Best practices

**Use this** to ensure your features are properly tested and tenant-isolated.

## 🏗️ Architecture Overview

### Technology Stack

**Backend:**
- **Laravel 12.x** - PHP framework
- **Filament 4.0** - Admin panel framework (TALL stack)
- **MySQL/PostgreSQL** - Primary database
- **Redis** - Caching and queues

**Frontend:**
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Livewire** - Server-side rendering

### Key Architectural Patterns

#### 1. Multi-Tenant Architecture

```
┌─────────────────────────────────────────────────┐
│              Commatix Platform                  │
├─────────────────────────────────────────────────┤
│  Tenant A    │  Tenant B    │  Tenant C         │
│  ├─ Users    │  ├─ Users    │  ├─ Users         │
│  ├─ Tasks    │  ├─ Tasks    │  ├─ Tasks         │
│  ├─ Divisions│  ├─ Divisions│  ├─ Divisions     │
│  └─ Data     │  └─ Data     │  └─ Data          │
└─────────────────────────────────────────────────┘
```

**Tenant Isolation Strategy:**
- Shared database with `tenant_id` column
- Row-level security via query scoping
- Auto-assignment of `tenant_id` in model boot methods
- Validation in service layer

#### 2. Role-Based Access Control (RBAC)

**User Hierarchy:**
```
Super Admin (System-wide access)
    │
    ├─ Tenant Admin (Full access to their tenant)
    │   │
    │   ├─ Tenant Manager (Manage users and workflows)
    │   │   │
    │   │   └─ Tenant User (Standard user)
    │   │       │
    │   │       └─ Tenant Viewer (Read-only)
```

**Permission Helpers:**
- `isSuperAdmin()` - Check if user is super admin
- `isTenantAdmin()` - Check if user is tenant admin
- `canManageUsers()` - Check if user can manage users
- `canManageWorkflows()` - Check if user can manage workflows

#### 3. Service Layer Pattern

```
┌──────────────────────────────────────────────┐
│           Filament Resources                 │
│          (UI Layer - View Logic)             │
└──────────────┬───────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────┐
│           Services                           │
│        (Business Logic Layer)                │
│  - TaskSchedulingService                     │
│  - CreditManagementService                   │
│  - NotificationService                       │
└──────────────┬───────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────┐
│           Models                             │
│         (Data Layer)                         │
│  - User, Task, Tenant, Division, etc.        │
└──────────────────────────────────────────────┘
```

**Why Service Layer?**
- Encapsulates complex business logic
- Reusable across controllers and resources
- Testable in isolation
- Enforces tenant validation
- Coordinates multiple models

#### 4. Navigation Structure

**Navigation Groups:**
- **Dashboard** - Dashboard pages
- **Multi-Tenant Management** - Tenants, Subscriptions, Divisions, Usage
- **Workflow Engine** - Workflows, Tasks, Templates, Milestones
- **CRM** - Subscribers, Lists, Campaigns
- **User Management** - Users, Roles
- **Communication Hub** - Messages, Notifications
- **Analytics & Reports** - Reports, Analytics
- **System Administration** - Document Types, Industries, Global Settings

### Database Schema Patterns

#### Standard Table Structure

Every tenant-scoped table should follow this pattern:

```sql
CREATE TABLE examples (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    -- other columns --
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_tenant_id (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

#### Key Relationships

```
Tenant
  ├─ has many Users
  ├─ has many Divisions
  ├─ has many Tasks
  ├─ has many Subscribers
  └─ has many WorkflowTemplates

User
  ├─ belongs to Tenant
  ├─ belongs to UserType
  ├─ belongs to Division (optional)
  └─ has many Tasks (as assignee)

Division
  ├─ belongs to Tenant
  ├─ has many Users
  └─ has many WorkflowTemplates

Task
  ├─ belongs to Tenant
  ├─ belongs to User (assigned_to)
  ├─ belongs to Subscriber (optional)
  └─ belongs to WorkflowTemplate (optional)
```

## 🚀 Quick Start for New Features

### Before You Start

1. **Review the architecture docs** - Read MULTI_TENANT_GUIDELINES.md first
2. **Understand the user roles** - Know which roles should access your feature
3. **Check existing patterns** - Look at similar features for consistency
4. **Plan data structure** - Determine if your feature needs tenant_id

### Feature Development Checklist

#### 1. Model Setup
- [ ] Create migration with `tenant_id` column (if tenant-specific)
- [ ] Add foreign key constraint to tenants table
- [ ] Add index on `tenant_id`
- [ ] Add `tenant_id` to `$fillable`
- [ ] Implement `boot()` method for auto-assignment
- [ ] Add `tenant()` relationship
- [ ] Add proper casts

#### 2. Service Layer (if needed)
- [ ] Create interface in `app/Contracts/`
- [ ] Create service in `app/Services/`
- [ ] Implement tenant validation
- [ ] Use database transactions
- [ ] Add logging
- [ ] Handle exceptions
- [ ] Register in AppServiceProvider

#### 3. Filament Resource
- [ ] Create resource: `php artisan make:filament-resource Example`
- [ ] Implement `canAccess()` with role checks
- [ ] Implement `getEloquentQuery()` with tenant scoping
- [ ] Configure form with proper sections and tabs
- [ ] Scope relationship dropdowns with `modifyQueryUsing`
- [ ] Hide tenant selector from tenant admins
- [ ] Configure table with appropriate columns
- [ ] Add filters (with tenant filter for super admins)
- [ ] Add actions (view, edit, delete)
- [ ] Set navigation group and icon

#### 4. Testing
- [ ] Create unit tests for model
- [ ] Create unit tests for service (if applicable)
- [ ] Create feature tests for resource
- [ ] Test tenant isolation
- [ ] Test with different user roles
- [ ] Test edge cases
- [ ] Run full test suite

#### 5. Documentation
- [ ] Add inline code comments
- [ ] Update README if needed
- [ ] Add examples if introducing new patterns

### Example: Adding a New Feature

Let's say you're adding a "Campaign" feature for email marketing:

**1. Migration:**
```php
Schema::create('campaigns', function (Blueprint $table) {
    $table->id();
    $table->string('tenant_id')->index();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('status')->default('draft');
    $table->timestamp('scheduled_at')->nullable();
    $table->timestamps();

    $table->foreign('tenant_id')
        ->references('id')
        ->on('tenants')
        ->onDelete('cascade');
});
```

**2. Model:**
```php
class Campaign extends Model
{
    protected $fillable = ['name', 'description', 'status', 'tenant_id', 'scheduled_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tenant_id) && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

**3. Resource:**
```php
class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'CRM';

    public static function canAccess(): bool
    {
        return (auth()->user()?->isTenantAdmin() || auth()->user()?->isSuperAdmin()) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isTenantAdmin()) {
            return $query->where('tenant_id', auth()->user()->tenant_id);
        }

        return $query; // Super admins see all
    }

    // ... form() and table() methods
}
```

**4. Tests:**
```php
class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_automatically_assigns_tenant_id(): void
    {
        $user = User::factory()->tenantAdmin()->create();
        $this->actingAs($user);

        $campaign = Campaign::create(['name' => 'Test Campaign']);

        $this->assertEquals($user->tenant_id, $campaign->tenant_id);
    }

    public function test_tenant_admin_only_sees_own_campaigns(): void
    {
        // Test implementation
    }
}
```

## 🔍 Common Patterns

### Pattern 1: Tenant-Scoped Relationship Dropdown

```php
Forms\Components\Select::make('division_id')
    ->relationship(
        name: 'division',
        titleAttribute: 'name',
        modifyQueryUsing: fn (Builder $query) =>
            $query->where('tenant_id', auth()->user()->tenant_id)
    )
    ->searchable()
    ->preload()
```

### Pattern 2: Service with Tenant Validation

```php
public function processData(int|string $tenantId, array $data): Model
{
    // Validate tenant access
    $this->validateTenantAccess($tenantId);

    // Use transaction
    return DB::transaction(function () use ($tenantId, $data) {
        // Business logic here
    });
}

protected function validateTenantAccess(int|string $tenantId): void
{
    $user = auth()->user();

    if (!$user) {
        throw new \Exception('User not authenticated');
    }

    if ($user->isSuperAdmin()) {
        return;
    }

    if ($user->tenant_id !== $tenantId) {
        throw new \Exception('Unauthorized access to tenant data');
    }
}
```

### Pattern 3: Role-Based Widget Display

```php
public function getWidgets(): array
{
    $user = auth()->user();

    if ($user->isSuperAdmin()) {
        return [
            SystemOverviewWidget::class,
            TenantGrowthChart::class,
        ];
    }

    if ($user->isTenantAdmin()) {
        return [
            TenantOverviewWidget::class,
            TenantActivityChart::class,
        ];
    }

    return [];
}
```

## ⚠️ Common Pitfalls

### ❌ Don't Do This

```php
// Relationship without tenant scoping
->relationship('division', 'name')

// Direct model query without scoping
Division::all()

// Trusting client-provided tenant_id
$division = Division::create($request->all());

// No tenant validation in service
public function processData(array $data) {
    return Model::create($data);
}
```

### ✅ Do This Instead

```php
// Scoped relationship
->relationship(
    name: 'division',
    titleAttribute: 'name',
    modifyQueryUsing: fn (Builder $query) =>
        $query->where('tenant_id', auth()->user()->tenant_id)
)

// Scoped query
Division::where('tenant_id', auth()->user()->tenant_id)->get()

// Auto-set tenant_id via model boot() method
protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
        if (empty($model->tenant_id) && auth()->check()) {
            $model->tenant_id = auth()->user()->tenant_id;
        }
    });
}

// Validate in service
public function processData(int|string $tenantId, array $data) {
    $this->validateTenantAccess($tenantId);
    return Model::create([...$data, 'tenant_id' => $tenantId]);
}
```

## 📝 Code Style and Conventions

### Naming Conventions

- **Models:** Singular, PascalCase (e.g., `User`, `WorkflowTemplate`)
- **Controllers:** PascalCase with suffix (e.g., `TaskController`)
- **Services:** PascalCase with suffix (e.g., `TaskSchedulingService`)
- **Interfaces:** PascalCase with suffix (e.g., `TaskSchedulingInterface`)
- **Resources:** PascalCase with suffix (e.g., `UserResource`)
- **Variables:** camelCase (e.g., `$tenantId`, `$subscriberList`)
- **Database tables:** Plural, snake_case (e.g., `users`, `workflow_templates`)
- **Database columns:** snake_case (e.g., `tenant_id`, `created_at`)

### Code Organization

```
app/
├── Console/
│   └── Commands/
├── Contracts/              # Service interfaces
│   ├── TaskSchedulingInterface.php
│   └── CreditManagementInterface.php
├── Filament/
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
├── Providers/
└── Services/               # Business logic services
    ├── TaskSchedulingService.php
    └── CreditManagementService.php
```

## 🤝 Contributing

When contributing to Commatix:

1. **Follow the architecture guidelines** - Consistency is key
2. **Write tests** - All features must have tests
3. **Document your code** - Add comments for complex logic
4. **Review checklist** - Use the feature checklist before submitting
5. **Test tenant isolation** - Verify data cannot leak between tenants

## 📞 Getting Help

- Review relevant documentation file first
- Check existing similar features for examples
- Ask team members for clarification
- Create detailed issue descriptions if needed

## 🔄 Keeping Documentation Updated

This documentation should be updated when:
- New architectural patterns are introduced
- Significant refactoring occurs
- New conventions are established
- Common issues are discovered

---

**Last Updated:** 2025-10-09

**Version:** 1.0.0