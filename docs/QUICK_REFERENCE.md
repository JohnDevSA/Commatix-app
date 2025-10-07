# SOLID Architecture Quick Reference

## Available Services

### 1. Authorization Service
**Get it:** `app(AuthorizationServiceInterface::class)`

**Common Methods:**
```php
$authService->canAccessResource($user, 'users');
$authService->canManageUsers($user);
$authService->canAccessGlobalResources($user);
$authService->applyScopedQuery($user, $query);
$authService->canImpersonate($impersonator, $target);
```

### 2. Task Progression Service
**Get it:** `app(TaskProgressionInterface::class)`

**Common Methods:**
```php
$taskService->progressToNextMilestone($task, $user);
$taskService->startTask($task, $reason);
$taskService->canProgress($task);
$taskService->getCompletionPercentage($task);
$taskService->getRemainingMilestones($task);
```

### 3. Workflow Locking Service
**Get it:** `app(WorkflowLockingInterface::class)`

**Common Methods:**
```php
$lockService->lockMilestone($milestone);
$lockService->unlockMilestone($milestone);
$lockService->isLocked($milestone);
$lockService->canModify($milestone, $user);
$lockService->lockSystemTemplate($workflow);
```

## When to Use What

| Scenario | Use |
|----------|-----|
| Simple attribute check | Model method |
| Complex business logic | Service |
| Data relationships | Eloquent relationships |
| Authorization check | AuthorizationService or User model helper |
| Task progression | TaskProgressionService |
| Milestone locking | WorkflowLockService |
| Query scoping | Eloquent scopes or AuthorizationService |

## Common Patterns

### Constructor Injection
```php
class MyController extends Controller
{
    public function __construct(
        private TaskProgressionInterface $taskService
    ) {}
}
```

### App Helper
```php
$service = app(TaskProgressionInterface::class);
```

### In Models
```php
public function myMethod()
{
    return app(ServiceInterface::class)->doSomething($this);
}
```

### In Filament Actions
```php
Tables\Actions\Action::make('action_name')
    ->action(function ($record) {
        app(ServiceInterface::class)->handle($record);
    });
```

## File Locations

- **Interfaces:** `app/Contracts/Services/`
- **Implementations:** `app/Services/*/`
- **Service Provider:** `app/Providers/SolidServiceProvider.php`
- **Documentation:** `docs/`

## Testing

### Unit Test a Service
```php
$service = new MyService();
$result = $service->doSomething($input);
$this->assertEquals($expected, $result);
```

### Mock a Service
```php
$mock = Mockery::mock(ServiceInterface::class);
$mock->shouldReceive('method')->andReturn($value);
$this->app->instance(ServiceInterface::class, $mock);
```

## Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Check service bindings
php artisan tinker
>>> app(TaskProgressionInterface::class)
```

