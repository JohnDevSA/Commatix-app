# Auto Task Scheduling Feature

This document explains how the auto task scheduling feature works in Commatix, including user assignment strategies and credit management.

## Table of Contents
1. [Overview](#overview)
2. [Task Scheduling](#task-scheduling)
3. [User Assignment Strategies](#user-assignment-strategies)
4. [Credit Management](#credit-management)
5. [API Endpoints](#api-endpoints)
6. [Usage Examples](#usage-examples)

## Overview

The auto task scheduling feature allows administrators to automatically create tasks for subscribers in a list, with flexible user assignment options and credit management for communication channels.

## Task Scheduling

The task scheduling system works by:
1. Taking a subscriber list as input
2. Creating individual tasks for each subscriber
3. Optionally assigning users to tasks based on configured strategies

### Key Components

- `TaskSchedulingService` - Main service for scheduling tasks
- `TaskSchedulingInterface` - Contract for task scheduling functionality
- `TaskRepositoryInterface` - Data access for tasks

## User Assignment Strategies

Two user assignment strategies are currently implemented:

### 1. Single User Assignment
Assigns all tasks to a single specified user.

### 2. Round Robin Assignment
Distributes tasks evenly among a group of users.

### Creating Custom Assignment Strategies

To create a custom assignment strategy:
1. Implement the `UserAssignmentStrategyInterface`
2. Register it in the service provider
3. Use it when scheduling tasks

```php
class CustomAssignmentStrategy implements UserAssignmentStrategyInterface
{
    public function assignUser(Task $task, Collection $users): User
    {
        // Custom logic here
    }
}
```

## Credit Management

The credit management system tracks and controls usage of communication channels:
- SMS
- Email
- WhatsApp
- Voice calls

### How It Works

1. Tenants have subscription packages that define credit limits
2. Usage is tracked monthly for each channel
3. Credits can be topped up by administrators
4. System checks available credits before sending communications

### Key Components

- `CreditManagementService` - Main service for credit operations
- `CreditManagementInterface` - Contract for credit management functionality

## API Endpoints

### Task Scheduling
```
POST /api/tasks/schedule
```

Parameters:
- `subscriber_list_id` (required) - ID of the subscriber list
- `task_data` (required) - Task details
- `assignment_strategy` (required) - Either "single" or "round-robin"
- `assigned_user_id` (optional) - User ID for single assignment

### Credit Management
```
GET /api/credits/{tenantId}/{channel}
POST /api/credits/{tenantId}/topup
GET /api/credits/{tenantId}/{channel}/can-use
```

## Usage Examples

### Scheduling Tasks with Round Robin Assignment

```php
// In a controller or service
$subscriberList = SubscriberList::find(1);
$users = User::where('tenant_id', $subscriberList->tenant_id)->get();

$taskData = [
    'title' => 'Follow up call',
    'description' => 'Call subscriber to discuss product',
    'priority' => 'medium',
    'due_date' => now()->addDays(3)
];

$tasks = $taskSchedulingService->scheduleTasksForSubscribers(
    $subscriberList,
    $taskData,
    $users
);
```

### Checking and Using Credits

```php
// Check if tenant can send SMS
if ($creditService->canUseChannel($tenant, 'sms', 10)) {
    // Send SMS
    $creditService->deductCredits($tenant, 'sms', 10);
}
```

### Topping Up Credits

```php
// Admin adds credits to tenant account
$creditService->addCredits($tenant, 'sms', 1000);
```

## For Future Developers

When extending this feature:

1. **Add new assignment strategies** by implementing `UserAssignmentStrategyInterface`
2. **Extend credit management** by adding new methods to `CreditManagementInterface`
3. **Create new scheduling logic** by extending `TaskSchedulingService`
4. **Follow SOLID principles** - keep classes focused on single responsibilities
5. **Use dependency injection** - inject dependencies through interfaces

## Common Patterns

1. **Interface-based design** - All major components use interfaces
2. **Strategy pattern** - User assignment uses strategy pattern
3. **Service pattern** - Business logic separated from controllers
4. **Repository pattern** - Data access abstracted through interfaces

## Troubleshooting

### "Insufficient credits" errors
Check the tenant's subscription package and current usage.

### "No users available" errors
Ensure the tenant has users when using round-robin assignment.

### Performance issues
Consider caching credit balances for frequently accessed tenants.