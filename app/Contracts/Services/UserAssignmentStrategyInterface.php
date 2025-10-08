<?php

namespace App\Contracts\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

interface UserAssignmentStrategyInterface
{
    /**
     * Assign a user to a task from the provided collection
     *
     * @param Task $task The task to assign
     * @param Collection $users Collection of users to choose from
     * @return User The selected user
     */
    public function assignUser(Task $task, Collection $users): User;
}