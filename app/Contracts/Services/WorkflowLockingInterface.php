<?php

namespace App\Contracts\Services;

use App\Models\Milestone;
use App\Models\User;
use App\Models\WorkflowTemplate;

/**
 * Workflow Locking Interface
 *
 * Manages locking and unlocking of workflow milestones to prevent unauthorized modifications.
 * This is particularly important for system templates and industry templates where certain
 * milestones must remain unchanged for compliance or standardization purposes.
 */
interface WorkflowLockingInterface
{
    /**
     * Lock a specific milestone to prevent modifications.
     *
     * @param  Milestone  $milestone  The milestone to lock
     */
    public function lockMilestone(Milestone $milestone): void;

    /**
     * Unlock a milestone to allow modifications.
     *
     * @param  Milestone  $milestone  The milestone to unlock
     */
    public function unlockMilestone(Milestone $milestone): void;

    /**
     * Lock multiple milestones for a workflow template.
     *
     * @param  WorkflowTemplate  $workflow  The workflow template
     * @param  array  $milestoneIds  Array of milestone IDs to lock
     */
    public function lockMilestones(WorkflowTemplate $workflow, array $milestoneIds): void;

    /**
     * Unlock multiple milestones for a workflow template.
     *
     * @param  WorkflowTemplate  $workflow  The workflow template
     * @param  array  $milestoneIds  Array of milestone IDs to unlock
     */
    public function unlockMilestones(WorkflowTemplate $workflow, array $milestoneIds): void;

    /**
     * Check if a milestone is locked.
     *
     * @param  Milestone  $milestone  The milestone to check
     * @return bool True if the milestone is locked
     */
    public function isLocked(Milestone $milestone): bool;

    /**
     * Check if a user can modify a milestone.
     *
     * Takes into account both the milestone's lock status and user permissions.
     *
     * @param  Milestone  $milestone  The milestone to check
     * @param  User  $user  The user attempting to modify
     * @return bool True if the user can modify the milestone
     */
    public function canModify(Milestone $milestone, User $user): bool;

    /**
     * Get all locked milestones for a workflow template.
     *
     * @param  WorkflowTemplate  $workflow  The workflow template
     * @return array Array of locked milestone IDs
     */
    public function getLockedMilestones(WorkflowTemplate $workflow): array;

    /**
     * Lock all milestones for a system template.
     *
     * System templates should have all milestones locked by default.
     *
     * @param  WorkflowTemplate  $workflow  The system workflow template
     */
    public function lockSystemTemplate(WorkflowTemplate $workflow): void;
}
