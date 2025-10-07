<?php

namespace App\Services;

use App\Interfaces\WorkflowLockingInterface;
use App\Models\User;
use App\Models\WorkflowTemplate;

class WorkflowLockService implements WorkflowLockingInterface
{
    /**
     * Lock a workflow for editing by a specific user
     */
    public function lock(WorkflowTemplate $workflow, User $user, string $reason = 'Configuring milestones'): void
    {
        $workflow->update([
            'is_locked' => true,
            'locked_by_user_id' => $user->id,
            'locked_at' => now(),
            'lock_reason' => $reason,
        ]);
    }

    /**
     * Unlock a workflow
     */
    public function unlock(WorkflowTemplate $workflow): void
    {
        $workflow->update([
            'is_locked' => false,
            'locked_by_user_id' => null,
            'locked_at' => null,
            'lock_reason' => null,
        ]);
    }

    /**
     * Check if a workflow is locked by a specific user
     */
    public function isLockedBy(WorkflowTemplate $workflow, User $user): bool
    {
        return $workflow->is_locked && $workflow->locked_by_user_id === $user->id;
    }

    /**
     * Check if a user can edit a workflow
     */
    public function canBeEditedBy(WorkflowTemplate $workflow, User $user): bool
    {
        return !$workflow->is_locked || $workflow->locked_by_user_id === $user->id;
    }
}