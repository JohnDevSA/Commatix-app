<?php

namespace App\Services\UserAssignment;

use App\Interfaces\UserAssignmentStrategyInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class RoundRobinAssignmentStrategy implements UserAssignmentStrategyInterface
{
    /**
     * Assign users to tasks in a round-robin fashion
     */
    public function assignUser(Task $task, Collection $users): User
    {
        // Simple round-robin implementation
        // In a real implementation, you might want to track the last assigned user
        // and use a more sophisticated approach
        
        static $lastIndex = -1;
        $lastIndex = ($lastIndex + 1) % $users->count();
        
        return $users->get($lastIndex);
    }
}