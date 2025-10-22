<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * All authenticated users can view the task list (filtered by visibleTo scope)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * Visibility rules:
     * - Super admins: see all tasks
     * - Tenant admins: see all tasks in their tenant
     * - Regular users: see tasks in their division OR tasks assigned to them
     */
    public function view(User $user, Task $task): bool
    {
        // Super admins see everything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check tenant isolation
        if ($task->tenant_id !== $user->tenant_id) {
            return false;
        }

        // Tenant admins see all tasks in their tenant
        if ($user->isTenantAdmin()) {
            return true;
        }

        // Regular users see:
        // 1. Tasks directly assigned to them
        // 2. Tasks in their division (if they belong to a division)
        return $task->assigned_to === $user->id
            || ($user->division_id && $task->division_id === $user->division_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * Tenant admins and super admins can create tasks
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * Users can update tasks if:
     * - They are super admin or tenant admin
     * - They are assigned to the task
     * - The task is in their tenant
     */
    public function update(User $user, Task $task): bool
    {
        // Super admins can update anything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check tenant isolation
        if ($task->tenant_id !== $user->tenant_id) {
            return false;
        }

        // Tenant admins can update all tasks in their tenant
        if ($user->isTenantAdmin()) {
            return true;
        }

        // Regular users can only update tasks assigned to them
        return $task->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * Only tenant admins and super admins can delete tasks
     */
    public function delete(User $user, Task $task): bool
    {
        // Super admins can delete anything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check tenant isolation
        if ($task->tenant_id !== $user->tenant_id) {
            return false;
        }

        // Only tenant admins can delete tasks
        return $user->isTenantAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $this->delete($user, $task);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->isSuperAdmin();
    }
}
