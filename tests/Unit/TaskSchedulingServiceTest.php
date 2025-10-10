<?php

namespace Tests\Unit;

use App\Contracts\Services\TaskSchedulingInterface;
use App\Models\Subscriber;
use App\Models\SubscriberList;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Task\TaskSchedulingService;
use App\Services\UserAssignment\RoundRobinAssignmentStrategy;
use App\Services\UserAssignment\SingleUserAssignmentStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskSchedulingServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskSchedulingService $service;

    private Tenant $tenant;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskSchedulingService;

        // Create test tenant and user
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->actingAs($this->user);
    }

    public function test_implements_interface()
    {
        $this->assertInstanceOf(TaskSchedulingInterface::class, $this->service);
    }

    public function test_schedules_tasks_for_all_subscribers()
    {
        // Arrange
        $subscriberList = SubscriberList::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscribers = Subscriber::factory()->count(5)->create(['tenant_id' => $this->tenant->id]);
        $subscriberList->subscribers()->attach($subscribers->pluck('id'));

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 'medium',
            'status' => 'draft',
        ];

        // Act
        $tasks = $this->service->scheduleTasksForSubscribers($subscriberList, $taskData);

        // Assert
        $this->assertCount(5, $tasks);
        $this->assertEquals(5, Task::count());

        foreach ($tasks as $task) {
            $this->assertEquals('Test Task', $task->title);
            $this->assertEquals('medium', $task->priority);
            $this->assertEquals($this->tenant->id, $task->tenant_id);
            $this->assertNotNull($task->subscriber_id);
        }
    }

    public function test_assigns_tasks_using_round_robin_strategy()
    {
        // Arrange
        $subscriberList = SubscriberList::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscribers = Subscriber::factory()->count(6)->create(['tenant_id' => $this->tenant->id]);
        $subscriberList->subscribers()->attach($subscribers->pluck('id'));

        $users = User::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

        $taskData = [
            'title' => 'Assigned Task',
            'priority' => 'high',
        ];

        $this->service->setAssignmentStrategy(new RoundRobinAssignmentStrategy);

        // Act
        $tasks = $this->service->scheduleTasksForSubscribers($subscriberList, $taskData, $users);

        // Assert
        $this->assertCount(6, $tasks);

        // Verify round-robin distribution (2 tasks per user)
        $assignmentCounts = $tasks->groupBy('assigned_to')->map->count();
        $this->assertEquals(3, $assignmentCounts->count());
        $this->assertTrue($assignmentCounts->every(fn ($count) => $count === 2));
    }

    public function test_assigns_all_tasks_to_single_user()
    {
        // Arrange
        $subscriberList = SubscriberList::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscribers = Subscriber::factory()->count(4)->create(['tenant_id' => $this->tenant->id]);
        $subscriberList->subscribers()->attach($subscribers->pluck('id'));

        $targetUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $users = collect([$targetUser]);

        $taskData = [
            'title' => 'Single User Task',
            'priority' => 'low',
        ];

        $this->service->setAssignmentStrategy(new SingleUserAssignmentStrategy($targetUser));

        // Act
        $tasks = $this->service->scheduleTasksForSubscribers($subscriberList, $taskData, $users);

        // Assert
        $this->assertCount(4, $tasks);
        $this->assertTrue($tasks->every(fn ($task) => $task->assigned_to === $targetUser->id));
    }

    public function test_throws_exception_when_subscriber_list_is_empty()
    {
        // Arrange
        $subscriberList = SubscriberList::factory()->create(['tenant_id' => $this->tenant->id]);
        $taskData = ['title' => 'Test'];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('has no subscribers');

        $this->service->scheduleTasksForSubscribers($subscriberList, $taskData);
    }

    public function test_creates_tasks_with_all_specified_fields()
    {
        // Arrange
        $subscriberList = SubscriberList::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscriber = Subscriber::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscriberList->subscribers()->attach($subscriber->id);

        $taskData = [
            'title' => 'Complete Task Data',
            'description' => 'Full description',
            'priority' => 'urgent',
            'status' => 'scheduled',
            'scheduled_start_date' => now()->addDay(),
            'due_date' => now()->addDays(7),
        ];

        // Act
        $tasks = $this->service->scheduleTasksForSubscribers($subscriberList, $taskData);

        // Assert
        $task = $tasks->first();
        $this->assertEquals('Complete Task Data', $task->title);
        $this->assertEquals('Full description', $task->description);
        $this->assertEquals('urgent', $task->priority);
        $this->assertEquals('scheduled', $task->status);
        $this->assertNotNull($task->scheduled_start_date);
        $this->assertNotNull($task->due_date);
    }

    public function test_rollback_on_error()
    {
        // Arrange
        $subscriberList = SubscriberList::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscriber = Subscriber::factory()->create(['tenant_id' => $this->tenant->id]);
        $subscriberList->subscribers()->attach($subscriber->id);

        // Invalid task data (missing required field)
        $taskData = [
            'description' => 'Missing title',
        ];

        // Act & Assert
        $this->expectException(\Exception::class);

        try {
            $this->service->scheduleTasksForSubscribers($subscriberList, $taskData);
        } catch (\Exception $e) {
            // Verify no tasks were created due to rollback
            $this->assertEquals(0, Task::count());
            throw $e;
        }
    }
}
