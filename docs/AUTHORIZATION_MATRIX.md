# Authorization Matrix

## Overview
This document outlines the complete authorization structure for the multi-tenant Commatix application, including user role permissions and resource access controls.

## User Roles

### 1. Super Admin
- **Full system access**: Can view, create, edit, and delete all resources across all tenants
- **Impersonation**: Can impersonate any user (except other Super Admins)
- **Global resource management**: Complete control over system-wide configurations
- **Cannot be impersonated**: Security measure to protect system administrators

### 2. Tenant Admin
- **Tenant-level management**: Can manage users, workflows, and tasks within their own tenant
- **User management**: Can create, edit, and manage users within their tenant
- **Workflow management**: Can create and customize workflow templates for their tenant
- **Task management**: Can view and manage all tasks within their tenant
- **Can be impersonated**: Super Admins can impersonate Tenant Admins

### 3. Tenant User
- **Operational access**: Can perform day-to-day operations within their tenant
- **Task operations**: Can create, view, and complete tasks assigned to them
- **Workflow viewing**: Can view workflow templates available to their tenant
- **Milestone updates**: Can update and approve milestones on tasks they're working on
- **Can be impersonated**: Super Admins can impersonate Tenant Users

## Resource Authorization Matrix

| Resource | Super Admin | Tenant Admin | Tenant User | Authorization Method |
|----------|-------------|--------------|-------------|---------------------|
| **Tenants** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **Users** | Full Access | Tenant-Scoped | No Access | `canManageUsers()` + Query Scoping |
| **Document Types** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **User Types** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **Industries** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **Global Workflows** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **Milestones** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **Tenant Workflows** | Full Access | Tenant-Scoped | View Only | `canManageWorkflows()` + Query Scoping |
| **Tasks** | Full Access | Tenant-Scoped | Tenant-Scoped | Query Scoping |
| **Subscriptions** | Full Access | No Access | No Access | `canAccessGlobalResources()` |
| **Usage Monitoring** | Full Access | No Access | No Access | `canAccessGlobalResources()` |

## Authorization Helper Methods

Located in `app/Models/User.php`:

### canAccessGlobalResources()
```php
public function canAccessGlobalResources(): bool
{
    return $this->isSuperAdmin();
}
```
**Usage**: Super Admin-only resources (Tenants, Document Types, User Types, Industries, Global Workflows, Milestones, Subscriptions, Usage)

### canManageTenant()
```php
public function canManageTenant(): bool
{
    return $this->isTenantAdmin() || $this->isSuperAdmin();
}
```
**Usage**: Tenant-level administrative operations

### canManageUsers()
```php
public function canManageUsers(): bool
{
    return $this->isSuperAdmin() || $this->isTenantAdmin();
}
```
**Usage**: UserResource access control

### canManageWorkflows()
```php
public function canManageWorkflows(): bool
{
    return $this->isSuperAdmin() || $this->isTenantAdmin();
}
```
**Usage**: TenantWorkflowTemplateResource access control

### canViewTenantData()
```php
public function canViewTenantData(string $tenantId): bool
{
    return $this->isSuperAdmin() || $this->tenant_id === $tenantId;
}
```
**Usage**: Checking if a user can view specific tenant data

### getTenantScopeQuery()
```php
public function getTenantScopeQuery(Builder $query): Builder
{
    if ($this->isSuperAdmin()) {
        return $query;
    }
    return $query->where('tenant_id', $this->tenant_id);
}
```
**Usage**: Scoping queries to tenant-specific data

## Implementation Details

### Super Admin Resources (Global Access)
These resources are accessible ONLY to Super Admins:

1. **TenantResource** (`app/Filament/Resources/TenantResource.php:24-27`)
2. **DocumentTypeResource** (`app/Filament/Resources/DocumentTypeResource.php:25-28`)
3. **UserTypeResource** (`app/Filament/Resources/UserTypeResource.php:26-29`)
4. **IndustryResource** (`app/Filament/Resources/IndustryResource.php:26-29`)
5. **WorkflowTemplateResource** (`app/Filament/Resources/WorkflowTemplateResource.php:31-34`)
6. **MilestoneResource** (`app/Filament/Resources/MilestoneResource.php:28-31`)
7. **TenantSubscriptionResource** (`app/Filament/Resources/TenantSubscriptionResource.php:26-29`)
8. **TenantUsageResource** (`app/Filament/Resources/TenantUsageResource.php:26-29`)

**Pattern**:
```php
public static function canAccess(): bool
{
    return auth()->user()?->canAccessGlobalResources() ?? false;
}
```

### Tenant-Scoped Resources
These resources are accessible to Super Admins (all data) and Tenant Admins/Users (tenant-specific data):

1. **UserResource** (`app/Filament/Resources/UserResource.php:31-51`)
   - Access: `canManageUsers()`
   - Scoping: Super Admins see all users, Tenant users see only their tenant's users

2. **TaskResource** (`app/Filament/Resources/TaskResource.php:31-46`)
   - Access: Available to all authenticated users
   - Scoping: Super Admins see all tasks, Tenant users see only their tenant's tasks

3. **TenantWorkflowTemplateResource** (`app/Filament/Resources/TenantWorkflowTemplateResource.php:33-36, 127-152`)
   - Access: `canManageWorkflows()`
   - Scoping: Complex query with published system templates, user's own custom templates, and industry-matching templates

**Pattern**:
```php
// Access control
public static function canAccess(): bool
{
    return auth()->user()?->canManageUsers() ?? false; // or canManageWorkflows()
}

// Query scoping
public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if (!$user) {
        return $query->whereRaw('1 = 0');
    }

    if ($user->isSuperAdmin()) {
        return $query; // Super admins see all
    }

    // Tenant users only see their tenant's data
    return $query->where('tenant_id', $user->tenant_id);
}
```

## Impersonation

### Configuration
- **Package**: `stechstudio/filament-impersonate` (v3.16)
- **Trait**: `Lab404\Impersonate\Models\Impersonate` (added to User model)

### Impersonation Rules
```php
// Who can impersonate
public function canImpersonate(): bool
{
    return $this->isSuperAdmin();
}

// Who can be impersonated
public function canBeImpersonated(): bool
{
    return !$this->isSuperAdmin();
}
```

### Usage
Impersonate action is available in UserResource table actions:
```php
\STS\FilamentImpersonate\Tables\Actions\Impersonate::make(),
```

## Testing Authorization

### Test as Super Admin
1. Login as Super Admin
2. Verify access to all resources:
   - Tenants
   - Users (all tenants)
   - Document Types
   - User Types
   - Industries
   - Global Workflows
   - Milestones
   - Tenant Workflows
   - Tasks (all tenants)
   - Subscriptions
   - Usage Monitoring

### Test as Tenant Admin
1. Login as Super Admin
2. Impersonate a Tenant Admin user
3. Verify access to:
   - Users (own tenant only)
   - Tenant Workflows (own tenant + published templates)
   - Tasks (own tenant only)
4. Verify NO access to:
   - Tenants
   - Document Types
   - User Types
   - Industries
   - Global Workflows
   - Milestones
   - Subscriptions
   - Usage Monitoring

### Test as Tenant User
1. Login as Super Admin
2. Impersonate a Tenant User
3. Verify limited access:
   - Can view tasks from their tenant
   - Can view workflow templates available to them
4. Verify NO access to:
   - User management
   - All Super Admin resources

## Security Best Practices

1. **Always check user authentication**: Use `auth()->user()` with null coalescing (`?? false`)
2. **Implement both canAccess() and getEloquentQuery()**: Access control at resource level AND query level
3. **Super Admin bypass**: Always allow Super Admins to see all data
4. **Tenant isolation**: Ensure non-Super Admin users can only access their tenant's data
5. **Impersonation protection**: Super Admins cannot be impersonated
6. **Clear caches after changes**: Run `php artisan optimize:clear` after authorization changes

## Future Enhancements

1. **Granular permissions**: Implement role-based permissions for specific actions (view, create, edit, delete)
2. **Tenant User permissions**: Add ability for Tenant Users to have different permission levels
3. **Audit logging**: Track impersonation sessions and user actions
4. **Permission caching**: Cache user permissions for improved performance
5. **Permission UI**: Admin interface to manage role permissions without code changes

## Related Files

- User Model: `app/Models/User.php`
- Resources: `app/Filament/Resources/*Resource.php`
- Impersonation Config: `composer.json` (stechstudio/filament-impersonate)
- Panel Provider: `app/Providers/Filament/AppPanelProvider.php`

## Version History

- **2025-10-07**: Initial comprehensive authorization implementation
  - Added authorization helper methods to User model
  - Implemented canAccess() on all resources
  - Added tenant scoping to UserResource, TaskResource, TenantWorkflowTemplateResource
  - Integrated Filament impersonation plugin
  - Standardized authorization checks using helper methods