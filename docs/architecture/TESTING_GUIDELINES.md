# Testing Guidelines

## Overview

Testing is critical for maintaining a robust multi-tenant application. This guide covers testing strategies, patterns, and best practices for the Commatix platform.

## Testing Stack

- **PHPUnit** - PHP testing framework
- **Laravel Testing Utilities** - Database factories, seeders, assertions
- **RefreshDatabase** - Reset database between tests
- **Pest** (optional) - Modern testing framework (can be added later)

## Test Structure

```
tests/
├── Unit/              # Isolated unit tests (models, services)
├── Feature/           # Integration tests (controllers, resources)
└── Browser/           # End-to-end tests (Laravel Dusk - optional)
```

## Multi-Tenant Testing Principles

### 1. Test Tenant Isolation

**Every test** must verify that:
- Users can only access their own tenant's data
- Cross-tenant queries return empty results
- Super admins can access all tenants
- Tenant admins are restricted to their tenant

### 2. Test User Roles

Test with different user types:
- Super Admin
- Tenant Admin
- Tenant Manager
- Tenant User
- Unauthenticated users

### 3. Test Data Scoping

Verify that:
- Queries are scoped by tenant_id
- Relationship dropdowns only show tenant-scoped options
- Bulk operations don't affect other tenants' data

## Unit Test Patterns

### Testing Models

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Division;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_automatically_assigns_tenant_id_on_creation(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user);

        $division = Division::create([
            'name' => 'Sales Department',
        ]);

        $this->assertEquals($tenant->id, $division->tenant_id);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $division = Division::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $division->tenant);
        $this->assertEquals($tenant->id, $division->tenant->id);
    }

    public function test_has_many_users(): void
    {
        $tenant = Tenant::factory()->create();
        $division = Division::factory()->create(['tenant_id' => $tenant->id]);

        $users = User::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
            'division_id' => $division->id,
        ]);

        $this->assertCount(3, $division->users);
        $this->assertEquals($users->pluck('id')->sort()->values(), $division->users->pluck('id')->sort()->values());
    }

    public function test_tenant_id_is_fillable(): void
    {
        $division = new Division([
            'name' => 'Test Division',
            'tenant_id' => 'test-tenant-id',
        ]);

        $this->assertEquals('test-tenant-id', $division->tenant_id);
    }
}
```

### Testing Services

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TaskSchedulingService;
use App\Contracts\TaskSchedulingInterface;
use App\Models\User;
use App\Models\Tenant;
use App\Models\SubscriberList;
use App\Models\Subscriber;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskSchedulingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskSchedulingInterface $service;
    protected User $tenantAdmin;
    protected User $otherTenantAdmin;
    protected Tenant $tenant;
    protected Tenant $otherTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(TaskSchedulingInterface::class);

        // Setup main tenant
        $this->tenant = Tenant::factory()->create(['name' => 'Tenant A']);
        $this->tenantAdmin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Setup other tenant for cross-tenant tests
        $this->otherTenant = Tenant::factory()->create(['name' => 'Tenant B']);
        $this->otherTenantAdmin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $this->otherTenant->id,
        ]);
    }

    public function test_schedules_tasks_for_subscribers(): void
    {
        $this->actingAs($this->tenantAdmin);

        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $subscribers = Subscriber::factory()->count(5)->create([
            'subscriber_list_id' => $subscriberList->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $taskData = [
            'title' => 'Follow up with subscriber',
            'description' => 'Call to discuss services',
            'priority' => 'high',
        ];

        $assignees = User::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $tasks = $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            $taskData,
            $assignees
        );

        // Assertions
        $this->assertCount(5, $tasks);

        $tasks->each(function ($task) use ($taskData) {
            $this->assertEquals($taskData['title'], $task->title);
            $this->assertEquals($taskData['description'], $task->description);
            $this->assertEquals($taskData['priority'], $task->priority);
            $this->assertEquals($this->tenant->id, $task->tenant_id);
            $this->assertEquals('pending', $task->status);
            $this->assertNotNull($task->assigned_to);
        });
    }

    public function test_prevents_cross_tenant_access(): void
    {
        // Login as tenant A admin
        $this->actingAs($this->tenantAdmin);

        // Try to schedule tasks for tenant B's subscriber list
        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $this->otherTenant->id,
        ]);

        Subscriber::factory()->count(3)->create([
            'subscriber_list_id' => $subscriberList->id,
            'tenant_id' => $this->otherTenant->id,
            'status' => 'active',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized access to tenant data');

        $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            ['title' => 'Test Task', 'priority' => 'medium'],
            collect()
        );
    }

    public function test_super_admin_can_access_all_tenants(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin);

        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        Subscriber::factory()->count(3)->create([
            'subscriber_list_id' => $subscriberList->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $tasks = $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            ['title' => 'Test Task', 'priority' => 'medium'],
            collect()
        );

        $this->assertCount(3, $tasks);
    }

    public function test_round_robin_assignment_distributes_evenly(): void
    {
        $this->actingAs($this->tenantAdmin);

        $tasks = Task::factory()->count(10)->create([
            'tenant_id' => $this->tenant->id,
            'assigned_to' => null,
        ]);

        $users = User::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->service->assignTasksRoundRobin($tasks, $users);

        // Verify distribution
        $tasks = $tasks->fresh();
        $assignments = $tasks->groupBy('assigned_to')->map->count();

        $this->assertCount(3, $assignments);

        // Each user should have 3 or 4 tasks (10 tasks / 3 users)
        $assignments->each(function ($count) {
            $this->assertGreaterThanOrEqual(3, $count);
            $this->assertLessThanOrEqual(4, $count);
        });
    }

    public function test_throws_exception_when_no_active_subscribers(): void
    {
        $this->actingAs($this->tenantAdmin);

        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Create only inactive subscribers
        Subscriber::factory()->count(3)->create([
            'subscriber_list_id' => $subscriberList->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'inactive',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No active subscribers found in the list');

        $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            ['title' => 'Test Task', 'priority' => 'medium'],
            collect()
        );
    }

    public function test_requires_authentication(): void
    {
        // Not logged in
        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');

        $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            ['title' => 'Test Task', 'priority' => 'medium'],
            collect()
        );
    }
}
```

### Testing Model Scopes

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_filters_by_status(): void
    {
        $tenant = Tenant::factory()->create();

        Task::factory()->count(5)->create([
            'tenant_id' => $tenant->id,
            'status' => 'pending',
        ]);

        Task::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
            'status' => 'completed',
        ]);

        $pendingTasks = Task::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->count();

        $this->assertEquals(5, $pendingTasks);
    }

    public function test_scope_filters_by_tenant(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        Task::factory()->count(5)->create(['tenant_id' => $tenant1->id]);
        Task::factory()->count(3)->create(['tenant_id' => $tenant2->id]);

        $tenant1Tasks = Task::where('tenant_id', $tenant1->id)->count();
        $tenant2Tasks = Task::where('tenant_id', $tenant2->id)->count();

        $this->assertEquals(5, $tenant1Tasks);
        $this->assertEquals(3, $tenant2Tasks);
    }
}
```

## Feature Test Patterns

### Testing Filament Resources

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Filament\Resources\DivisionResource;

class DivisionResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $tenantAdmin;
    protected User $otherTenantAdmin;
    protected Tenant $tenant;
    protected Tenant $otherTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->otherTenant = Tenant::factory()->create();

        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->tenantAdmin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        $this->otherTenantAdmin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $this->otherTenant->id,
        ]);
    }

    public function test_tenant_admin_can_access_division_resource(): void
    {
        $this->actingAs($this->tenantAdmin);

        $this->assertTrue(DivisionResource::canAccess());
    }

    public function test_regular_user_cannot_access_division_resource(): void
    {
        $regularUser = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->actingAs($regularUser);

        $this->assertFalse(DivisionResource::canAccess());
    }

    public function test_tenant_admin_only_sees_own_divisions(): void
    {
        $this->actingAs($this->tenantAdmin);

        // Create divisions for both tenants
        $myDivisions = Division::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $otherDivisions = Division::factory()->count(2)->create([
            'tenant_id' => $this->otherTenant->id,
        ]);

        $query = DivisionResource::getEloquentQuery();
        $results = $query->get();

        $this->assertCount(3, $results);
        $this->assertEquals(
            $myDivisions->pluck('id')->sort()->values(),
            $results->pluck('id')->sort()->values()
        );
    }

    public function test_super_admin_sees_all_divisions(): void
    {
        $this->actingAs($this->superAdmin);

        Division::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);
        Division::factory()->count(2)->create(['tenant_id' => $this->otherTenant->id]);

        $query = DivisionResource::getEloquentQuery();
        $results = $query->get();

        $this->assertCount(5, $results);
    }

    public function test_tenant_admin_can_create_division(): void
    {
        $this->actingAs($this->tenantAdmin);

        Livewire::test(DivisionResource\Pages\CreateDivision::class)
            ->fillForm([
                'name' => 'New Division',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('divisions', [
            'name' => 'New Division',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_tenant_admin_cannot_see_tenant_selector(): void
    {
        $this->actingAs($this->tenantAdmin);

        Livewire::test(DivisionResource\Pages\CreateDivision::class)
            ->assertFormFieldExists('name')
            ->assertFormFieldIsHidden('tenant_id');
    }

    public function test_super_admin_sees_tenant_selector(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(DivisionResource\Pages\CreateDivision::class)
            ->assertFormFieldExists('name')
            ->assertFormFieldIsVisible('tenant_id');
    }
}
```

### Testing API Endpoints

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $tenantAdmin;
    protected Tenant $tenant;
    protected Tenant $otherTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->otherTenant = Tenant::factory()->create();

        $this->tenantAdmin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_can_list_own_tenant_tasks(): void
    {
        $this->actingAs($this->tenantAdmin);

        Task::factory()->count(5)->create(['tenant_id' => $this->tenant->id]);
        Task::factory()->count(3)->create(['tenant_id' => $this->otherTenant->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_cannot_access_other_tenant_task(): void
    {
        $this->actingAs($this->tenantAdmin);

        $otherTask = Task::factory()->create([
            'tenant_id' => $this->otherTenant->id,
        ]);

        $response = $this->getJson("/api/tasks/{$otherTask->id}");

        $response->assertStatus(403);
    }

    public function test_can_create_task_with_auto_tenant_assignment(): void
    {
        $this->actingAs($this->tenantAdmin);

        $response = $this->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => 'high',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New Task',
                    'tenant_id' => $this->tenant->id,
                ],
            ]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }
}
```

## Test Factories

### Creating Flexible Factories

```php
<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Tenant;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'tenant_id' => Tenant::factory(),
            'user_type_id' => UserType::factory(),
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type_id' => UserType::where('name', 'Super Admin')->first()?->id
                ?? UserType::factory()->create(['name' => 'Super Admin'])->id,
            'tenant_id' => null,
        ]);
    }

    /**
     * Indicate that the user is a tenant admin.
     */
    public function tenantAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type_id' => UserType::where('name', 'Tenant Admin')->first()?->id
                ?? UserType::factory()->create(['name' => 'Tenant Admin'])->id,
        ]);
    }

    /**
     * Indicate that the user is a tenant manager.
     */
    public function tenantManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type_id' => UserType::where('name', 'Tenant Manager')->first()?->id
                ?? UserType::factory()->create(['name' => 'Tenant Manager'])->id,
        ]);
    }

    /**
     * Indicate that the user is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```

## Testing Checklist

Use this checklist for every feature:

### Model Tests
- [ ] Auto-assignment of tenant_id works
- [ ] Relationships are properly defined
- [ ] Fillable fields include tenant_id
- [ ] Casts are properly defined

### Resource Tests
- [ ] Super admin can access
- [ ] Tenant admin can access (if appropriate)
- [ ] Regular users cannot access (if appropriate)
- [ ] Query scoping works correctly
- [ ] Tenant admin only sees own data
- [ ] Super admin sees all data
- [ ] Tenant selector hidden for tenant admins
- [ ] Relationship dropdowns are tenant-scoped

### Service Tests
- [ ] Validates tenant access
- [ ] Prevents cross-tenant access
- [ ] Super admin can access all tenants
- [ ] Requires authentication
- [ ] Handles errors gracefully
- [ ] Uses database transactions
- [ ] Returns correct types

### API Tests
- [ ] Requires authentication
- [ ] Scopes data by tenant
- [ ] Returns 403 for cross-tenant access
- [ ] Auto-assigns tenant_id on creation
- [ ] Validates input data

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Unit/TaskSchedulingServiceTest.php

# Run specific test method
php artisan test --filter=test_schedules_tasks_for_subscribers

# Run with coverage
php artisan test --coverage

# Run parallel (faster)
php artisan test --parallel

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

## Continuous Integration

Add to your CI pipeline (GitHub Actions example):

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v2

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'

    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress

    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"

    - name: Generate Key
      run: php artisan key:generate

    - name: Run Tests
      run: php artisan test
```

## Best Practices

1. **Test tenant isolation in EVERY test**
2. **Use factories** for test data creation
3. **Clean database** between tests (RefreshDatabase)
4. **Test both positive and negative cases**
5. **Test with multiple user roles**
6. **Use descriptive test names**
7. **Keep tests fast** - use in-memory SQLite for unit tests
8. **Test edge cases** - empty results, invalid input, etc.
9. **Mock external services** - don't hit real APIs in tests
10. **Keep tests independent** - one test should not depend on another