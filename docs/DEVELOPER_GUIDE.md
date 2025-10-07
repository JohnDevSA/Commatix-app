# Developer Guide: Understanding the SOLID Architecture

This guide explains the SOLID architecture implemented in this codebase and how to work with it effectively.

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Key Design Patterns](#key-design-patterns)
3. [Directory Structure](#directory-structure)
4. [How to Add New Features](#how-to-add-new-features)
5. [Dependency Injection](#dependency-injection)
6. [Testing Guidelines](#testing-guidelines)

## Architecture Overview

The codebase follows SOLID principles to ensure maintainability and scalability:

1. **Single Responsibility Principle**: Each class has one reason to change
2. **Open/Closed Principle**: Entities are open for extension but closed for modification
3. **Liskov Substitution Principle**: Subtypes can replace their base types
4. **Interface Segregation Principle**: Clients depend only on methods they use
5. **Dependency Inversion Principle**: Depend on abstractions, not concretions

## Key Design Patterns

### Service Pattern
Business logic is separated from models into service classes:
- `WorkflowLockService` - Handles workflow locking operations
- `TaskProgressionService` - Manages task progression logic

### Repository Pattern
Data access is abstracted through repositories:
- `WorkflowRepository` - Handles workflow data operations
- `TaskRepository` - Handles task data operations

### Dependency Injection
Dependencies are injected through interfaces rather than instantiated directly.

## Directory Structure

```
app/
├── Interfaces/          # Contract definitions
├── Services/            # Business logic implementations
├── Repositories/        # Data access abstractions
├── Models/              # Data models (minimal logic)
├── Http/Controllers/    # HTTP request handlers
└── Providers/           # Service providers
```

## How to Add New Features

### 1. Identify the Responsibility
Determine which class or service should handle the new functionality based on its responsibility.

### 2. Create or Extend Interfaces
If you need new functionality, create or extend appropriate interfaces in the `Interfaces/` directory.

### 3. Implement the Logic
Implement the new functionality in service classes, not in models.

### 4. Use Dependency Injection
Inject dependencies through constructor injection rather than instantiating classes directly.

### Example: Adding a New Workflow Feature

1. Create a new interface in `app/Interfaces/` if needed:
```php
interface WorkflowNotificationInterface
{
    public function sendNotification(WorkflowTemplate $workflow, string $message): void;
}
```

2. Create an implementation in `app/Services/`:
```php
class WorkflowNotificationService implements WorkflowNotificationInterface
{
    public function sendNotification(WorkflowTemplate $workflow, string $message): void
    {
        // Implementation here
    }
}
```

3. Register the service in `app/Providers/SolidServiceProvider.php`:
```php
$this->app->bind(
    WorkflowNotificationInterface::class,
    WorkflowNotificationService::class
);
```

4. Use it in your controller:
```php
public function __construct(WorkflowNotificationInterface $notificationService)
{
    $this->notificationService = $notificationService;
}
```

## Dependency Injection

The application uses Laravel's service container for dependency injection. Services are registered in `app/Providers/SolidServiceProvider.php`.

To inject a dependency:
1. Define it in the constructor
2. Type-hint the interface
3. Laravel will automatically resolve the implementation

```php
class MyController extends Controller
{
    private WorkflowLockingInterface $lockService;
    
    public function __construct(WorkflowLockingInterface $lockService)
    {
        $this->lockService = $lockService;
    }
}
```

## Testing Guidelines

1. **Test Services**: Each service should have corresponding unit tests
2. **Mock Dependencies**: Use interface-based mocking for dependencies
3. **Test Behavior, Not Implementation**: Focus on what the code does, not how it does it

Example test:
```php
public function test_workflow_can_be_locked()
{
    $workflow = Mockery::mock(WorkflowTemplate::class);
    $user = Mockery::mock(User::class);
    
    $service = new WorkflowLockService();
    // Test implementation
}
```

## Common Patterns to Follow

1. **Keep Models Thin**: Models should only contain data access logic
2. **Services for Business Logic**: All business rules go in service classes
3. **Repositories for Data Access**: Database queries belong in repositories
4. **Interfaces for Contracts**: Define behavior through interfaces
5. **Constructor Injection**: Inject dependencies through constructors

## Troubleshooting

### "Interface not found" errors
Make sure the interface is properly registered in `SolidServiceProvider.php`.

### "Method not found" errors
Check that your service implements all methods defined in the interface.

### Performance issues
Consider caching in repositories for frequently accessed data.

## Need Help?

1. Check the existing implementations for examples
2. Review the SOLID_PRINCIPLES.md documentation
3. Look at the unit tests for usage examples
4. Contact the team lead for architectural questions