# Service Layer Guidelines

## Purpose

The service layer encapsulates business logic, keeping it separate from controllers, resources, and models. Services handle complex operations, coordinate between multiple models, and ensure business rules are enforced.

## When to Create a Service

Create a service when you have:
- Complex business logic spanning multiple models
- Operations that need to be reused across controllers/resources
- External API integrations
- Background job processing
- Calculations or transformations
- File processing or uploads

## Service Structure

### Basic Service Structure

```php
<?php

namespace App\Services;

use App\Models\Example;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExampleService
{
    /**
     * Process an example with tenant isolation.
     */
    public function processExample(int|string $tenantId, array $data): Example
    {
        // Validate tenant ownership
        $this->validateTenantAccess($tenantId);

        // Perform business logic
        return DB::transaction(function () use ($tenantId, $data) {
            $example = $this->createExample($tenantId, $data);
            $this->performRelatedOperations($example);

            Log::info('Example processed', [
                'tenant_id' => $tenantId,
                'example_id' => $example->id,
            ]);

            return $example;
        });
    }

    /**
     * Validate tenant access.
     */
    protected function validateTenantAccess(int|string $tenantId): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Super admins can access all tenants
        if ($user->isSuperAdmin()) {
            return;
        }

        // Regular users can only access their own tenant
        if ($user->tenant_id !== $tenantId) {
            throw new \Exception('Unauthorized access to tenant data');
        }
    }

    /**
     * Create example with tenant isolation.
     */
    protected function createExample(int|string $tenantId, array $data): Example
    {
        return Example::create([
            ...$data,
            'tenant_id' => $tenantId,
        ]);
    }

    /**
     * Perform related operations.
     */
    protected function performRelatedOperations(Example $example): void
    {
        // Additional business logic
    }
}
```

## Service Interface Pattern

For services that may have multiple implementations, use interfaces:

```php
<?php

namespace App\Contracts;

use App\Models\Example;

interface ExampleServiceInterface
{
    public function processExample(int|string $tenantId, array $data): Example;

    public function getExamplesForTenant(int|string $tenantId): Collection;

    public function deleteExample(int|string $tenantId, int $exampleId): bool;
}
```

Then implement the interface:

```php
<?php

namespace App\Services;

use App\Contracts\ExampleServiceInterface;

class ExampleService implements ExampleServiceInterface
{
    // Implementation here
}
```

Register in `AppServiceProvider`:

```php
public function register(): void
{
    $this->app->bind(ExampleServiceInterface::class, ExampleService::class);
}
```

## Real-World Example: Task Scheduling Service

### Interface

```php
<?php

namespace App\Contracts;

use App\Models\SubscriberList;
use Illuminate\Support\Collection;

interface TaskSchedulingInterface
{
    /**
     * Schedule tasks for all subscribers in a list.
     *
     * @param SubscriberList $subscriberList
     * @param array $taskData ['title', 'description', 'priority', 'workflow_template_id']
     * @param Collection $assignees Collection of User models
     * @return Collection Collection of created Task models
     */
    public function scheduleTasksForSubscribers(
        SubscriberList $subscriberList,
        array $taskData,
        Collection $assignees
    ): Collection;

    /**
     * Assign tasks using round-robin algorithm.
     *
     * @param Collection $tasks
     * @param Collection $users
     * @return void
     */
    public function assignTasksRoundRobin(Collection $tasks, Collection $users): void;
}
```

### Implementation

```php
<?php

namespace App\Services;

use App\Contracts\TaskSchedulingInterface;
use App\Models\SubscriberList;
use App\Models\Task;
use App\Models\Subscriber;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskSchedulingService implements TaskSchedulingInterface
{
    public function scheduleTasksForSubscribers(
        SubscriberList $subscriberList,
        array $taskData,
        Collection $assignees
    ): Collection {
        // Validate tenant access
        $this->validateTenantAccess($subscriberList->tenant_id);

        return DB::transaction(function () use ($subscriberList, $taskData, $assignees) {
            // Get active subscribers
            $subscribers = Subscriber::where('subscriber_list_id', $subscriberList->id)
                ->where('status', 'active')
                ->get();

            if ($subscribers->isEmpty()) {
                throw new \Exception('No active subscribers found in the list');
            }

            // Create tasks
            $tasks = $subscribers->map(function ($subscriber) use ($taskData, $subscriberList) {
                return Task::create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'] ?? null,
                    'priority' => $taskData['priority'] ?? 'medium',
                    'status' => 'pending',
                    'tenant_id' => $subscriberList->tenant_id,
                    'subscriber_id' => $subscriber->id,
                    'workflow_template_id' => $taskData['workflow_template_id'] ?? null,
                    'created_by' => auth()->id(),
                ]);
            });

            // Assign tasks if assignees provided
            if ($assignees->isNotEmpty()) {
                $this->assignTasksRoundRobin($tasks, $assignees);
            }

            Log::info('Tasks scheduled for subscribers', [
                'tenant_id' => $subscriberList->tenant_id,
                'subscriber_list_id' => $subscriberList->id,
                'task_count' => $tasks->count(),
                'assignee_count' => $assignees->count(),
            ]);

            return $tasks;
        });
    }

    public function assignTasksRoundRobin(Collection $tasks, Collection $users): void
    {
        if ($users->isEmpty()) {
            return;
        }

        $userIndex = 0;
        $userCount = $users->count();

        foreach ($tasks as $task) {
            $user = $users[$userIndex];

            $task->update([
                'assigned_to' => $user->id,
            ]);

            $userIndex = ($userIndex + 1) % $userCount;
        }
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
}
```

## Credit Management Service Example

```php
<?php

namespace App\Services;

use App\Contracts\CreditManagementInterface;
use App\Models\Tenant;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\DB;

class CreditManagementService implements CreditManagementInterface
{
    public function addCredits(int|string $tenantId, int $amount, string $description): CreditTransaction
    {
        $this->validateTenantAccess($tenantId);

        return DB::transaction(function () use ($tenantId, $amount, $description) {
            $tenant = Tenant::findOrFail($tenantId);

            // Create transaction record
            $transaction = CreditTransaction::create([
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description,
                'balance_after' => $tenant->credits + $amount,
                'created_by' => auth()->id(),
            ]);

            // Update tenant balance
            $tenant->increment('credits', $amount);

            return $transaction;
        });
    }

    public function deductCredits(int|string $tenantId, int $amount, string $description): bool
    {
        $this->validateTenantAccess($tenantId);

        return DB::transaction(function () use ($tenantId, $amount, $description) {
            $tenant = Tenant::lockForUpdate()->findOrFail($tenantId);

            // Check if sufficient credits
            if ($tenant->credits < $amount) {
                throw new \Exception('Insufficient credits');
            }

            // Create transaction record
            CreditTransaction::create([
                'tenant_id' => $tenantId,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
                'balance_after' => $tenant->credits - $amount,
                'created_by' => auth()->id(),
            ]);

            // Update tenant balance
            $tenant->decrement('credits', $amount);

            return true;
        });
    }

    public function getBalance(int|string $tenantId): int
    {
        $this->validateTenantAccess($tenantId);

        return Tenant::findOrFail($tenantId)->credits;
    }

    public function getTransactionHistory(int|string $tenantId, int $limit = 50): Collection
    {
        $this->validateTenantAccess($tenantId);

        return CreditTransaction::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
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
}
```

## Service Best Practices

### 1. Tenant Isolation is CRITICAL

**Always** validate tenant access at the beginning of service methods:

```php
protected function validateTenantAccess(int|string $tenantId): void
{
    $user = auth()->user();

    if (!$user) {
        throw new \Exception('User not authenticated');
    }

    if ($user->isSuperAdmin()) {
        return; // Super admins can access all tenants
    }

    if ($user->tenant_id !== $tenantId) {
        throw new \Exception('Unauthorized access to tenant data');
    }
}
```

### 2. Use Database Transactions

Wrap complex operations in database transactions:

```php
return DB::transaction(function () use ($data) {
    $record1 = Model1::create($data);
    $record2 = Model2::create($data);

    // If any operation fails, all will be rolled back

    return $record1;
});
```

### 3. Type Hints

Use proper type hints for tenant IDs since they can be int or string:

```php
public function processData(int|string $tenantId, array $data): Model
{
    // Method implementation
}
```

### 4. Logging

Log important operations for debugging and auditing:

```php
use Illuminate\Support\Facades\Log;

Log::info('Operation completed', [
    'tenant_id' => $tenantId,
    'user_id' => auth()->id(),
    'operation' => 'create_task',
    'details' => $details,
]);
```

### 5. Exception Handling

Use descriptive exceptions:

```php
if (!$this->canPerformOperation($data)) {
    throw new \Exception('Operation cannot be performed: validation failed');
}

// Or use custom exceptions
throw new InsufficientCreditsException("Tenant {$tenantId} has insufficient credits");
```

### 6. Return Types

Be explicit about return types:

```php
public function getItems(int|string $tenantId): Collection
{
    return Model::where('tenant_id', $tenantId)->get();
}

public function createItem(int|string $tenantId, array $data): Model
{
    return Model::create([...$data, 'tenant_id' => $tenantId]);
}

public function deleteItem(int|string $tenantId, int $itemId): bool
{
    $item = Model::where('tenant_id', $tenantId)->findOrFail($itemId);
    return $item->delete();
}
```

### 7. Dependency Injection

Inject dependencies in constructor:

```php
class ExampleService
{
    public function __construct(
        protected NotificationService $notifications,
        protected AuditService $audit
    ) {}

    public function processExample(int|string $tenantId, array $data): Example
    {
        $example = Example::create([...$data, 'tenant_id' => $tenantId]);

        $this->notifications->sendNotification($example);
        $this->audit->logCreation($example);

        return $example;
    }
}
```

### 8. Method Organization

Organize methods logically:

```php
class ExampleService
{
    // Public methods (interface methods)
    public function createExample(int|string $tenantId, array $data): Example { }
    public function updateExample(int|string $tenantId, int $id, array $data): Example { }
    public function deleteExample(int|string $tenantId, int $id): bool { }

    // Protected helper methods
    protected function validateData(array $data): void { }
    protected function processRelations(Example $example, array $relations): void { }

    // Private utility methods
    private function formatData(array $data): array { }
}
```

## Testing Services

Services should be thoroughly tested:

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TaskSchedulingService;
use App\Models\User;
use App\Models\Tenant;
use App\Models\SubscriberList;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskSchedulingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskSchedulingService $service;
    protected User $tenantAdmin;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(TaskSchedulingService::class);

        $this->tenant = Tenant::factory()->create();
        $this->tenantAdmin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->actingAs($this->tenantAdmin);
    }

    public function test_schedules_tasks_for_subscribers(): void
    {
        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $subscribers = Subscriber::factory()->count(5)->create([
            'subscriber_list_id' => $subscriberList->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $taskData = [
            'title' => 'Test Task',
            'priority' => 'high',
        ];

        $users = User::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $tasks = $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            $taskData,
            $users
        );

        $this->assertCount(5, $tasks);
        $this->assertEquals('Test Task', $tasks->first()->title);
        $this->assertEquals($this->tenant->id, $tasks->first()->tenant_id);
    }

    public function test_prevents_cross_tenant_access(): void
    {
        $otherTenant = Tenant::factory()->create();
        $subscriberList = SubscriberList::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized access to tenant data');

        $this->service->scheduleTasksForSubscribers(
            $subscriberList,
            ['title' => 'Test'],
            collect()
        );
    }

    public function test_round_robin_assignment(): void
    {
        $tasks = Task::factory()->count(10)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $users = User::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->service->assignTasksRoundRobin($tasks, $users);

        $tasks->load('assignedTo');

        // Check that tasks are evenly distributed
        $assignments = $tasks->groupBy('assigned_to')->map->count();

        $this->assertEqualsWithDelta(3.33, $assignments->avg(), 0.5);
    }
}
```

## Service Registration

Register services in `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(
            \App\Contracts\TaskSchedulingInterface::class,
            \App\Services\TaskSchedulingService::class
        );

        $this->app->bind(
            \App\Contracts\CreditManagementInterface::class,
            \App\Services\CreditManagementService::class
        );

        // Singleton services (shared instance)
        $this->app->singleton(
            \App\Services\CacheService::class,
            \App\Services\CacheService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
```

## Usage in Controllers/Resources

Use dependency injection or the app helper:

```php
// In Filament Resource action
use App\Contracts\TaskSchedulingInterface;

Tables\Actions\Action::make('scheduleTasksAction')
    ->action(function (SubscriberList $record, array $data) {
        $schedulingService = app(TaskSchedulingInterface::class);

        $tasks = $schedulingService->scheduleTasksForSubscribers(
            $record,
            $data,
            $users
        );

        Notification::make()
            ->title("{$tasks->count()} tasks scheduled successfully")
            ->success()
            ->send();
    });
```

## Summary

✅ **DO:**
- Always validate tenant access
- Use database transactions for complex operations
- Return explicit types
- Log important operations
- Test thoroughly
- Use dependency injection
- Handle exceptions gracefully

❌ **DON'T:**
- Trust client-provided tenant_id
- Skip tenant validation
- Perform business logic in controllers/resources
- Skip error handling
- Forget to log important operations
- Use direct model queries without tenant scope
