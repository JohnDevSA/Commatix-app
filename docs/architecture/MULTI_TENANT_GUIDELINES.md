# Multi-Tenant Architecture Guidelines

## Core Principles

1. **Tenant Isolation**: All tenant-specific data MUST be scoped by `tenant_id`
2. **Role-Based Access**: Use `isSuperAdmin()`, `isTenantAdmin()`, `canManageUsers()` helpers
3. **Data Integrity**: Never allow cross-tenant data access or modification

## Checklist for New Features

### Models
- [ ] Add `tenant_id` column if model is tenant-specific
- [ ] Add `tenant_id` to `$fillable` array
- [ ] Implement `boot()` method to auto-set `tenant_id` on creation
- [ ] Add tenant relationship: `belongsTo(Tenant::class)`
- [ ] Add proper casts for `tenant_id` (integer/string)

**Example:**
```php
protected $fillable = ['name', 'tenant_id'];

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
```

### Filament Resources
- [ ] Implement `canAccess()` method with proper role checks
- [ ] Implement `getEloquentQuery()` to scope data by tenant
- [ ] Use `modifyQueryUsing` on relationship selects to scope dropdowns
- [ ] Set appropriate `navigationGroup`
- [ ] Hide tenant selector from tenant admins using `->visible(fn () => auth()->user()?->isSuperAdmin())`

**Example:**
```php
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

// In form() - for relationship dropdowns:
Forms\Components\Select::make('division_id')
    ->relationship(
        name: 'division',
        titleAttribute: 'name',
        modifyQueryUsing: fn (Builder $query) =>
            $query->where('tenant_id', auth()->user()->tenant_id)
    )
```

### Services
- [ ] Always accept or inject `tenant_id` parameter
- [ ] Validate tenant ownership before operations
- [ ] Never trust client-provided `tenant_id` - use authenticated user's tenant

### API Endpoints
- [ ] Scope all queries by authenticated user's tenant
- [ ] Validate tenant ownership in form requests
- [ ] Return 403 for cross-tenant access attempts

## Navigation Groups

Use these standardized groups:
- **Dashboard** - Dashboard pages
- **Multi-Tenant Management** - Tenants, Subscriptions, Divisions, Usage
- **Workflow Engine** - Workflows, Tasks, Templates, Milestones
- **CRM** - Subscribers, Lists, Campaigns
- **User Management** - Users, Roles
- **Communication Hub** - Messages, Notifications
- **Analytics & Reports** - Reports, Analytics
- **System Administration** - Document Types, Industries, Global Settings

## Common Pitfalls to Avoid

❌ **DON'T:**
```php
// Using relationship without scoping
->relationship('division', 'name')

// Querying without tenant scope
Division::all()

// Trusting client tenant_id
$division = Division::create($request->all());
```

✅ **DO:**
```php
// Scope relationship queries
->relationship(
    name: 'division',
    titleAttribute: 'name',
    modifyQueryUsing: fn (Builder $query) =>
        $query->where('tenant_id', auth()->user()->tenant_id)
)

// Always scope queries
Division::where('tenant_id', auth()->user()->tenant_id)->get()

// Auto-set tenant_id in model boot()
protected static function boot() { /* ... */ }
```

## Testing Checklist

- [ ] Test as Super Admin - should see all data
- [ ] Test as Tenant Admin - should only see own tenant data
- [ ] Test cross-tenant access attempts - should fail
- [ ] Test dropdowns only show tenant-scoped options
- [ ] Test auto-assignment of tenant_id on creation

## Database Migrations

Always add proper indexes:
```php
$table->string('tenant_id')->index();
$table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
```