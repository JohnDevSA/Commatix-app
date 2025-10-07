<?php

namespace App\Services\UserAssignment;

use App\Interfaces\UserAssignmentStrategyInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class SingleUserAssignmentStrategy implements UserAssignmentStrategyInterface
{
    private User $assignedUser;

    public function __construct(User $user)
    {
        $this->assignedUser = $user;
    }

    /**
     * Always assign to the same user
     */
    public function assignUser(Task $task, Collection $users): User
    {
        return $this->assignedUser;
    }
}