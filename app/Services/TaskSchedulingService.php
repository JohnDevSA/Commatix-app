<?php

namespace App\Services;

use App\Interfaces\TaskSchedulingInterface;
use App\Interfaces\UserAssignmentStrategyInterface;
use App\Models\SubscriberList;
use App\Models\User;
use App\Models\Task;
use App\Models\Subscriber;
use Illuminate\Support\Collection;
use App\Interfaces\TaskRepositoryInterface;

class TaskSchedulingService implements TaskSchedulingInterface
{
    private UserAssignmentStrategyInterface $assignmentStrategy;
    private TaskRepositoryInterface $taskRepository;

    public function __construct(
        UserAssignmentStrategyInterface $assignmentStrategy,
        TaskRepositoryInterface $taskRepository
    ) {
        $this->assignmentStrategy = $assignmentStrategy;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Schedule tasks for all subscribers in a list
     */
    public function scheduleTasksForSubscribers(
        SubscriberList $subscriberList, 
        array $taskData,
        ?Collection $users = null
    ): Collection {
        $tasks = collect();
        
        // Get all subscribers from the list
        $subscribers = $subscriberList->subscribers;
        
        foreach ($subscribers as $subscriber) {
            // Create task data for this subscriber
            $individualTaskData = array_merge($taskData, [
                'subscriber_id' => $subscriber->id,
                'tenant_id' => $subscriberList->tenant_id,
                'division_id' => $subscriberList->division_id,
            ]);
            
            // Create the task
            $task = $this->taskRepository->create($individualTaskData);
            
            // Assign user if users provided
            if ($users && $users->count() > 0) {
                $assignedUser = $this->assignTaskToUser($task, $users);
                $task->update(['assigned_to' => $assignedUser->id]);
            }
            
            $tasks->push($task);
        }
        
        return $tasks;
    }

    /**
     * Assign a task to a user based on the configured strategy
     */
    public function assignTaskToUser(Task $task, Collection $users): User
    {
        return $this->assignmentStrategy->assignUser($task, $users);
    }
}