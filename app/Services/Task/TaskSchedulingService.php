<?php

namespace App\Services\Task;

use App\Contracts\Services\TaskSchedulingInterface;
use App\Contracts\Services\UserAssignmentStrategyInterface;
use App\Models\SubscriberList;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskSchedulingService implements TaskSchedulingInterface
{
    public function __construct(
        private ?UserAssignmentStrategyInterface $assignmentStrategy = null
    ) {}

    /**
     * Schedule tasks for all subscribers in a list
     *
     * Creates individual tasks for each subscriber in the list,
     * optionally assigning them to users using the configured strategy.
     *
     * @param  SubscriberList  $subscriberList  The subscriber list to create tasks for
     * @param  array  $taskData  Task template data (title, description, priority, workflow_template_id, etc.)
     * @param  Collection|null  $users  Optional collection of users for round-robin assignment
     * @return Collection Collection of created Task models
     *
     * @throws \Exception If task creation fails
     */
    public function scheduleTasksForSubscribers(
        SubscriberList $subscriberList,
        array $taskData,
        ?Collection $users = null
    ): Collection {
        $createdTasks = collect();

        try {
            DB::beginTransaction();

            // Get all subscribers from the list
            $subscribers = $subscriberList->subscribers()->get();

            if ($subscribers->isEmpty()) {
                throw new \Exception("Subscriber list '{$subscriberList->name}' has no subscribers.");
            }

            foreach ($subscribers as $subscriber) {
                // Create task for this subscriber
                $task = Task::create([
                    'subscriber_id' => $subscriber->id,
                    'tenant_id' => $subscriberList->tenant_id,
                    'title' => $taskData['title'] ?? 'Scheduled Task',
                    'description' => $taskData['description'] ?? '',
                    'priority' => $taskData['priority'] ?? 'medium',
                    'status' => $taskData['status'] ?? 'draft',
                    'workflow_template_id' => $taskData['workflow_template_id'] ?? null,
                    'scheduled_start_date' => $taskData['scheduled_start_date'] ?? now(),
                    'due_date' => $taskData['due_date'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                // Assign user if users collection provided and strategy exists
                if ($users && $users->isNotEmpty() && $this->assignmentStrategy) {
                    $assignedUser = $this->assignTaskToUser($task, $users);
                    $task->update(['assigned_to' => $assignedUser->id]);
                }

                $createdTasks->push($task);
            }

            DB::commit();

            Log::info("Scheduled {$createdTasks->count()} tasks for subscriber list: {$subscriberList->name}");

            return $createdTasks;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to schedule tasks: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Assign a task to a user from the provided collection
     *
     * Uses the configured user assignment strategy to select a user.
     * If no strategy is configured, returns the first user.
     *
     * @param  Task  $task  The task to assign
     * @param  Collection  $users  Collection of users to choose from
     * @return User The user assigned to the task
     *
     * @throws \Exception If no users are available
     */
    public function assignTaskToUser(Task $task, Collection $users): User
    {
        if ($users->isEmpty()) {
            throw new \Exception('No users available for task assignment.');
        }

        // Use assignment strategy if available, otherwise pick first user
        if ($this->assignmentStrategy) {
            return $this->assignmentStrategy->assignUser($task, $users);
        }

        // Fallback: return first user
        return $users->first();
    }

    /**
     * Set the user assignment strategy
     */
    public function setAssignmentStrategy(UserAssignmentStrategyInterface $strategy): self
    {
        $this->assignmentStrategy = $strategy;

        return $this;
    }
}
