<?php

namespace App\Services\UserAssignment;

use App\Contracts\Services\UserAssignmentStrategyInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RoundRobinAssignmentStrategy implements UserAssignmentStrategyInterface
{
    /**
     * Assign a user to a task using round-robin distribution
     *
     * Distributes tasks evenly among the provided users by tracking
     * the last assigned user index and rotating through the collection.
     *
     * @param  Task  $task  The task to assign
     * @param  Collection  $users  Collection of users to distribute among
     * @return User The selected user
     *
     * @throws \Exception If no users are provided
     */
    public function assignUser(Task $task, Collection $users): User
    {
        if ($users->isEmpty()) {
            throw new \Exception('Cannot assign task: no users provided for round-robin assignment.');
        }

        // Generate cache key based on tenant and workflow template
        $cacheKey = $this->getCacheKey($task);

        // Get the last used index from cache (default to -1)
        $lastIndex = Cache::get($cacheKey, -1);

        // Calculate next index (wraps around)
        $nextIndex = ($lastIndex + 1) % $users->count();

        // Get user at the next index
        $selectedUser = $users->values()->get($nextIndex);

        // Store the new index in cache for 24 hours
        Cache::put($cacheKey, $nextIndex, now()->addHours(24));

        return $selectedUser;
    }

    /**
     * Generate cache key for round-robin state
     */
    private function getCacheKey(Task $task): string
    {
        return sprintf(
            'round_robin:tenant_%d:workflow_%d',
            $task->tenant_id,
            $task->workflow_template_id ?? 0
        );
    }
}
