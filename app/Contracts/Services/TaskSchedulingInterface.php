<?php

namespace App\Contracts\Services;

use App\Models\SubscriberList;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskSchedulingInterface
{
    /**
     * Schedule tasks for all subscribers in a list
     *
     * @param SubscriberList $subscriberList The subscriber list to create tasks for
     * @param array $taskData Task template data (title, description, priority, etc.)
     * @param Collection|null $users Optional collection of users for assignment
     * @return Collection Collection of created Task models
     */
    public function scheduleTasksForSubscribers(
        SubscriberList $subscriberList,
        array $taskData,
        ?Collection $users = null
    ): Collection;

    /**
     * Assign a task to a user from the provided collection
     *
     * @param Task $task The task to assign
     * @param Collection $users Collection of users to choose from
     * @return User The user assigned to the task
     */
    public function assignTaskToUser(Task $task, Collection $users): User;
}