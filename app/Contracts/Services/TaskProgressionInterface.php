<?php

namespace App\Contracts\Services;

use App\Models\Task;
use App\Models\TaskMilestone;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Task Progression Interface
 *
 * Manages the progression of tasks through their workflow milestones.
 * Handles complex business logic around task state transitions, milestone completion,
 * and validation of progression requirements.
 */
interface TaskProgressionInterface
{
    /**
     * Progress a task to the next milestone in the workflow.
     *
     * @param  Task  $task  The task to progress
     * @param  User|null  $user  The user performing the action (for audit trail)
     * @return TaskMilestone|null The new current milestone, or null if task is complete
     *
     * @throws \Exception If progression is not allowed or validation fails
     */
    public function progressToNextMilestone(Task $task, ?User $user = null): ?TaskMilestone;

    /**
     * Complete the current milestone for a task.
     *
     * @param  Task  $task  The task
     * @param  User  $user  The user completing the milestone
     * @param  array  $data  Additional data (notes, attachments, etc.)
     * @return bool True if milestone was successfully completed
     */
    public function completeMilestone(Task $task, User $user, array $data = []): bool;

    /**
     * Check if a task can progress to the next milestone.
     *
     * Validates all requirements (documents, approvals, etc.)
     *
     * @param  Task  $task  The task to check
     * @return bool True if the task can progress
     */
    public function canProgress(Task $task): bool;

    /**
     * Get the next milestone in the workflow sequence.
     *
     * @param  Task  $task  The task
     * @return TaskMilestone|null The next milestone, or null if task is complete
     */
    public function getNextMilestone(Task $task): ?TaskMilestone;

    /**
     * Get all remaining milestones for a task.
     *
     * @param  Task  $task  The task
     * @return Collection Collection of remaining TaskMilestone instances
     */
    public function getRemainingMilestones(Task $task): Collection;

    /**
     * Calculate task completion percentage based on milestones.
     *
     * @param  Task  $task  The task
     * @return float Completion percentage (0-100)
     */
    public function getCompletionPercentage(Task $task): float;

    /**
     * Start a task (move from draft/scheduled to in_progress).
     *
     * @param  Task  $task  The task to start
     * @param  string|null  $reason  Optional reason for early start
     * @return bool True if task was successfully started
     */
    public function startTask(Task $task, ?string $reason = null): bool;

    /**
     * Check if a task can be started.
     *
     * @param  Task  $task  The task to check
     * @return bool True if the task can be started
     */
    public function canStartTask(Task $task): bool;

    /**
     * Revert a task to a previous milestone.
     *
     * @param  Task  $task  The task
     * @param  TaskMilestone  $milestone  The milestone to revert to
     * @param  User  $user  The user performing the reversion
     * @param  string  $reason  Reason for reversion
     * @return bool True if successfully reverted
     */
    public function revertToMilestone(Task $task, TaskMilestone $milestone, User $user, string $reason): bool;

    /**
     * Check if all required documents are attached for current milestone.
     *
     * @param  Task  $task  The task
     * @return bool True if all required documents are present
     */
    public function hasRequiredDocuments(Task $task): bool;

    /**
     * Get validation errors preventing task progression.
     *
     * @param  Task  $task  The task
     * @return array Array of validation error messages
     */
    public function getProgressionErrors(Task $task): array;
}
