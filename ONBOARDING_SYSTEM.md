# Commatix Onboarding System - Technical Documentation

## 🎯 Overview

The Commatix onboarding system is a robust, production-ready implementation that creates fully initialized multi-tenant environments with proper database setup, admin user creation, and Redis caching for optimal performance.

## 🏗️ Architecture

### Key Components

1. **OnboardingService** (`app/Services/OnboardingService.php`)
   - Handles all tenant creation logic
   - Manages database transactions
   - Creates tenant databases
   - Initializes tenant admin users
   - Handles divisions and team invites

2. **OnboardingControllerModern** (`app/Http/Controllers/OnboardingControllerModern.php`)
   - Monday.com-style progressive disclosure UI
   - 6-step onboarding flow
   - Uses OnboardingService for tenant creation
   - Leverages cached models for performance

3. **Cached Models**
   - `Province` model with Redis caching (24-hour TTL)
   - `Industry` model with Redis caching (24-hour TTL)
   - Automatic cache invalidation on updates

4. **OnboardingProgress** model
   - Tracks completion status for each step
   - Stores step data in JSON field
   - Provides completion percentage

## 📊 Data Flow

```
User Registration
    ↓
OnboardingControllerModern::index()
    ↓
Step 1-6 (Progressive Disclosure)
    ↓ (Each step saves to OnboardingProgress)
OnboardingControllerModern::processStep()
    ↓
Step 6 Complete → OnboardingService::completeOnboarding()
    ↓
├─ Validate all step data
├─ Create Tenant (central database)
├─ Run tenant migrations
├─ Create tenant admin user
├─ Create divisions (if specified)
├─ Send team invites (if specified)
└─ Fire OnboardingCompleted event
    ↓
Redirect to celebration page
```

## 🗄️ Database Architecture

### Central Database Tables

- `tenants` - Tenant information (multi-tenancy)
- `sa_provinces` - South African provinces (cached)
- `industries` - Industry classifications (cached)
- `onboarding_progress` - Tracks user onboarding progress
- `user_types` - User type definitions
- `workflow_templates` - Template workflows

### Tenant Database

Each tenant gets its own database schema with:
- `users` - Tenant-specific users
- `divisions` - Organizational divisions
- `approval_groups` - Approval workflows
- `tasks`, `workflows`, `campaigns`, etc.

## ⚡ Redis Caching Strategy

### Province Caching

```php
// Cache keys
'provinces:all' => All provinces collection (24h TTL)
'provinces:all:options' => Select options array (24h TTL)
'provinces:code:GP' => Individual province (24h TTL)
```

**Usage:**
```php
$provinces = Province::getAllCached();
$options = Province::getSelectOptions(); // For dropdowns
$gauteng = Province::findByCodeCached('GP');
```

**Cache Invalidation:**
- Automatic on create/update/delete via model events
- Manual: `Province::clearCache()`

### Industry Caching

```php
// Cache keys
'industries:active:all' => Active industries (24h TTL)
'industries:active:options' => Select options (24h TTL)
'industries:id:1' => Individual industry (24h TTL)
'industries:code:tech' => Industry by code (24h TTL)
```

**Usage:**
```php
$industries = Industry::getAllActiveCached();
$options = Industry::getSelectOptions(); // For dropdowns
$tech = Industry::findByCodeCached('technology');
```

**Cache Invalidation:**
- Automatic on create/update/delete
- Manual: `Industry::clearCache()`

## 🔐 Tenant Creation Process

### Step-by-Step Breakdown

1. **Data Validation**
   ```php
   $validation = $this->validateStepData($allStepData);
   // Checks: company_name, registration_number, industry_id,
   //         email, phone, address, POPIA consent
   ```

2. **Tenant Creation**
   ```php
   $tenant = Tenant::create([
       'id' => 'tenant_' . Str::uuid(),
       'unique_code' => $this->generateUniqueCode($companyName),
       // ... all tenant fields
   ]);
   ```

3. **Tenant Database Initialization**
   ```php
   // Run tenant migrations
   Artisan::call('tenants:migrate', ['--tenants' => [$tenant->id]]);

   // Initialize tenant context
   Tenancy::initialize($tenant);
   ```

4. **Admin User Creation**
   ```php
   // Create user in TENANT database (not central!)
   $tenantUser = User::create([
       'name' => $centralUser->name,
       'email' => $centralUser->email,
       'password' => $centralUser->password, // Already hashed
       'tenant_id' => $tenant->id,
   ]);

   // Assign tenant_admin role
   $tenantUser->assignRole('tenant_admin');
   ```

5. **Optional Components**
   - Create divisions if specified
   - Queue team invitation emails
   - Set subscription tier limits

## 📝 Onboarding Steps

### Step 1: Company Information
- Company name, registration number
- Industry selection (cached from database)
- Company size
- Contact information
- Physical & postal address
- Province selection (cached from database)

### Step 2: Team & Role Setup
- User role definition
- User type selection
- Optional: Divisions creation
- Optional: Team member invitations

### Step 3: Use Case Selection
- Primary use case
- Workflow template selection

### Step 4: Integrations
- Optional third-party integrations
- Google Workspace, Microsoft 365, etc.

### Step 5: POPIA Compliance
- Privacy policy acceptance (required)
- Terms of service acceptance (required)
- Marketing consent (optional)

### Step 6: Subscription Plan
- Plan selection (Starter, Professional, Enterprise)
- Billing cycle (Monthly/Annual)

## 🚀 Deployment & Setup

### 1. Run Migrations

```bash
# Central database migrations
php artisan migrate

# This will create:
# - sa_provinces
# - industries
# - onboarding_progress
# - tenants (if not exists)
```

### 2. Seed Reference Data

```bash
# Seed provinces and industries
php artisan db:seed --class=ProvinceSeeder
php artisan db:seed --class=IndustrySeeder
```

### 3. Warm Up Caches

```bash
# Pre-cache provinces and industries
php artisan tinker
>>> Province::getAllCached();
>>> Industry::getAllActiveCached();
```

### 4. Configure Redis

**Option A: Production (Redis)**
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Option B: Development (File/Database)**
```env
CACHE_DRIVER=file
# or
CACHE_DRIVER=database
```

### 5. Test Onboarding Flow

```bash
# 1. Register a new user
# 2. Visit /onboarding-modern
# 3. Complete all 6 steps
# 4. Verify:
#    - Tenant created in central DB
#    - Tenant database created
#    - Admin user created in tenant DB
#    - User has tenant_admin role
```

## 🧪 Testing

### Unit Tests

```php
// Test Province caching
public function test_provinces_are_cached()
{
    Province::clearCache();

    // First call - hits database
    $provinces1 = Province::getAllCached();

    // Second call - hits cache
    $provinces2 = Province::getAllCached();

    $this->assertEquals($provinces1, $provinces2);
    $this->assertTrue(Cache::has(Province::CACHE_KEY_ALL));
}

// Test Industry caching
public function test_industries_are_cached()
{
    Industry::clearCache();

    $industries = Industry::getAllActiveCached();

    $this->assertGreaterThan(0, $industries->count());
    $this->assertTrue(Cache::has(Industry::CACHE_KEY_ALL_ACTIVE));
}
```

### Feature Tests

```php
// Test complete onboarding flow
public function test_complete_onboarding_creates_tenant_and_admin()
{
    $user = User::factory()->create();

    // Simulate completing all 6 steps
    $progress = OnboardingProgress::create([
        'user_id' => $user->id,
        'current_step' => 6,
    ]);

    // Add step data
    $progress->saveStepData(1, [
        'company_name' => 'Test Company',
        'company_registration_number' => '2024/123456/07',
        // ... all required fields
    ]);

    // Complete onboarding
    $service = new OnboardingService();
    $result = $service->completeOnboarding($user, $progress);

    $this->assertTrue($result['success']);
    $this->assertInstanceOf(Tenant::class, $result['tenant']);

    // Verify tenant admin exists
    Tenancy::initialize($result['tenant']);
    $adminUser = User::where('email', $user->email)->first();
    $this->assertTrue($adminUser->hasRole('tenant_admin'));
}
```

## 🔍 Monitoring & Debugging

### Logs

```php
// OnboardingService logs key events:
Log::info('Onboarding completed successfully', [
    'tenant_id' => $tenant->id,
    'user_id' => $user->id,
]);

Log::error('Onboarding completion failed', [
    'user_id' => $user->id,
    'error' => $e->getMessage(),
]);
```

### Cache Monitoring

```bash
# Check Redis cache
redis-cli
> KEYS provinces:*
> KEYS industries:*
> GET provinces:all
> TTL provinces:all
```

### Database Verification

```sql
-- Check tenant was created
SELECT * FROM tenants WHERE id = 'tenant_xxx';

-- Check onboarding progress
SELECT * FROM onboarding_progress WHERE user_id = xxx;

-- Verify tenant database exists
SHOW DATABASES LIKE 'tenant%';

-- Check tenant admin user (switch to tenant DB first)
USE tenant_xxx;
SELECT * FROM users WHERE email = 'user@example.com';
SELECT * FROM model_has_roles WHERE model_id = xxx;
```

## ⚠️ Troubleshooting

### Issue: "Industry options not showing"

**Cause:** Database not seeded or cache empty

**Solution:**
```bash
php artisan db:seed --class=IndustrySeeder
php artisan tinker
>>> Industry::clearCache();
>>> Industry::getAllActiveCached();
```

### Issue: "Province dropdown empty"

**Cause:** Provinces table not seeded

**Solution:**
```bash
php artisan db:seed --class=ProvinceSeeder
>>> Province::clearCache();
```

### Issue: "Tenant created but no admin user"

**Cause:** Tenant migrations not run or tenant context not initialized

**Solution:**
```bash
# Run tenant migrations manually
php artisan tenants:migrate --tenants=tenant_xxx

# Check tenant database
mysql -u root -p tenant_xxx
> SELECT * FROM users;
```

### Issue: "Redis connection failed"

**Cause:** Redis not running or wrong config

**Solution:**
```bash
# Start Redis
sudo service redis-server start

# Or use file cache for development
# In .env:
CACHE_DRIVER=file
```

## 📈 Performance Metrics

### Expected Performance

- **Province Loading:** <5ms (cached)
- **Industry Loading:** <5ms (cached)
- **Onboarding Completion:** <2s (includes DB creation)
- **Cache Hit Rate:** >95% after warm-up

### Optimization Tips

1. **Pre-warm caches** on application deployment
2. **Use Redis** in production for best performance
3. **Monitor cache hit rates** with Laravel Telescope
4. **Index foreign keys** in tenant databases

## 🎨 UI/UX Features

- **Progressive Disclosure:** One question at a time
- **Auto-advance:** Radio selections auto-proceed
- **Keyboard Navigation:** Enter to continue, Alt+Arrows
- **Progress Indicator:** Visual progress bar + step dots
- **Session Persistence:** Can resume if interrupted
- **Mobile Responsive:** Works on all screen sizes

## 🔒 Security Considerations

1. **CSRF Protection:** All forms have CSRF tokens
2. **Input Validation:** Server-side validation on every step
3. **Database Transactions:** Rollback on failure
4. **Tenant Isolation:** Strict tenant context enforcement
5. **Password Hashing:** Bcrypt (copied from central user)
6. **POPIA Compliance:** Required consent tracking

## 📚 Additional Resources

- [Multi-Tenancy Documentation](https://tenancyforlaravel.com/docs)
- [Laravel Caching](https://laravel.com/docs/12.x/cache)
- [Spatie Permissions](https://spatie.be/docs/laravel-permission)
- [Filament Admin Panel](https://filamentphp.com/docs)

## 🤝 Contributing

When extending the onboarding system:

1. **Always use the OnboardingService** for tenant creation
2. **Leverage cached models** (Province, Industry)
3. **Add validation** for new fields in `validateStepData()`
4. **Update step views** in `resources/views/onboarding/`
5. **Write tests** for new functionality
6. **Clear caches** when reference data changes

---

**Last Updated:** 2025-10-19
**Version:** 2.0 (Robust Multi-Tenant Implementation)
