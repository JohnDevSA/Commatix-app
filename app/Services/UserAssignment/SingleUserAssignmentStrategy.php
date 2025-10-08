<?php

namespace App\Services\UserAssignment;

use App\Contracts\Services\UserAssignmentStrategyInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class SingleUserAssignmentStrategy implements UserAssignmentStrategyInterface
{
    private ?User $selectedUser = null;

    /**
     * Create a new single user assignment strategy
     *
     * @param User|null $user The user to assign all tasks to (optional, can be set later)
     */
    public function __construct(?User $user = null)
    {
        $this->selectedUser = $user;
    }

    /**
     * Assign all tasks to a single specified user
     *
     * Always returns the same user for consistent assignment.
     * If no user was set in constructor, uses the first user from the collection.
     *
     * @param Task $task The task to assign
     * @param Collection $users Collection of users (uses first if no user pre-selected)
     * @return User The selected user
     * @throws \Exception If no user is selected and collection is empty
     */
    public function assignUser(Task $task, Collection $users): User
    {
        // If we have a pre-selected user, always use that
        if ($this->selectedUser) {
            return $this->selectedUser;
        }

        // Otherwise, use the first user from the collection
        if ($users->isEmpty()) {
            throw new \Exception("Cannot assign task: no users provided for single user assignment.");
        }

        // Cache the selected user for future assignments
        $this->selectedUser = $users->first();

        return $this->selectedUser;
    }

    /**
     * Set the user to assign all tasks to
     *
     * @param User $user
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->selectedUser = $user;
        return $this;
    }
}