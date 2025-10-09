---
description: Test multi-tenancy functionality
---

Create a test scenario to verify multi-tenancy is working correctly in Commatix.

**Test Steps:**
1. Check current tenants in database: `php artisan tinker` → `Tenant::all()`
2. Create a test tenant if needed
3. Run tenant-aware operations:
   - Switch tenant context
   - Create tenant-scoped resources (divisions, users, workflows)
   - Verify data isolation between tenants
4. Check tenant database connections
5. Verify tenant-specific routes and middleware

**Key Areas to Test:**
- Tenant isolation (one tenant can't see another's data)
- Tenant switching works correctly
- Filament resources respect tenant scope
- User permissions within tenants
- Division and approval group scoping

**Commands:**
```php
// In tinker
Tenant::all();
tenancy()->initialize($tenant);
// Test tenant-scoped queries
```
