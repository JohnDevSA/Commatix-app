<?php

namespace App\Services\Task;

use App\Contracts\Services\TaskProgressionInterface;
use App\Models\Task;
use App\Models\TaskMilestone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Task Progression Service
 *
 * Manages the progression of tasks through their workflow milestones.
 * Implements complex business logic for task state transitions,
 * milestone completion validation, and progression requirements.
 *
 * @package App\Services\Task
 */
class TaskProgressionService implements TaskProgressionInterface
{
    /**
     * {@inheritDoc}
     */
    public function progressToNextMilestone(Task $task, ?User $user = null): ?TaskMilestone
    {
        if (!$this->canProgress($task)) {
            $errors = $this->getProgressionErrors($task);
            throw new \Exception('Cannot progress task: ' . implode(', ', $errors));
        }

        return DB::transaction(function () use ($task, $user) {
            // Complete current milestone if exists
            if ($task->current_milestone_id) {
                $currentTaskMilestone = $task->taskMilestones()
                    ->where('milestone_id', $task->current_milestone_id)
                    ->first();

                if ($currentTaskMilestone) {
                    $currentTaskMilestone->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'completed_by' => $user?->id,
                    ]);
                }
            }

            // Get next milestone
            $nextMilestone = $this->getNextMilestone($task);

            if (!$nextMilestone) {
                // Task is complete
                $task->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'current_milestone_id' => null,
                ]);

                Log::info("Task {$task->id} completed by user {$user?->id}");

                return null;
            }

            // Update task to next milestone
            $task->update([
                'current_milestone_id' => $nextMilestone->milestone_id,
                'status' => 'in_progress',
            ]);

            // Update task milestone status
            $nextMilestone->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            Log::info("Task {$task->id} progressed to milestone {$nextMilestone->milestone_id}");

            return $nextMilestone;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function completeMilestone(Task $task, User $user, array $data = []): bool
    {
        if (!$task->current_milestone_id) {
            return false;
        }

        $taskMilestone = $task->taskMilestones()
            ->where('milestone_id', $task->current_milestone_id)
            ->first();

        if (!$taskMilestone) {
            return false;
        }

        // Check if required documents are present
        if (!$this->hasRequiredDocuments($task)) {
            return false;
        }

        $taskMilestone->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => $user->id,
            'completion_notes' => $data['notes'] ?? null,
        ]);

        Log::info("Milestone {$taskMilestone->milestone_id} completed for task {$task->id} by user {$user->id}");

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canProgress(Task $task): bool
    {
        return empty($this->getProgressionErrors($task));
    }

    /**
     * {@inheritDoc}
     */
    public function getNextMilestone(Task $task): ?TaskMilestone
    {
        $currentSequence = 0;

        if ($task->current_milestone_id) {
            $current = $task->taskMilestones()
                ->where('milestone_id', $task->current_milestone_id)
                ->first();

            $currentSequence = $current?->sequence_order ?? 0;
        }

        return $task->taskMilestones()
            ->where('sequence_order', '>', $currentSequence)
            ->where('status', '!=', 'completed')
            ->orderBy('sequence_order', 'asc')
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getRemainingMilestones(Task $task): Collection
    {
        $currentSequence = 0;

        if ($task->current_milestone_id) {
            $current = $task->taskMilestones()
                ->where('milestone_id', $task->current_milestone_id)
                ->first();

            $currentSequence = $current?->sequence_order ?? 0;
        }

        return $task->taskMilestones()
            ->where('sequence_order', '>=', $currentSequence)
            ->where('status', '!=', 'completed')
            ->orderBy('sequence_order', 'asc')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getCompletionPercentage(Task $task): float
    {
        $total = $task->taskMilestones()->count();

        if ($total === 0) {
            return 0.0;
        }

        $completed = $task->taskMilestones()
            ->where('status', 'completed')
            ->count();

        return round(($completed / $total) * 100, 2);
    }

    /**
     * {@inheritDoc}
     */
    public function startTask(Task $task, ?string $reason = null): bool
    {
        if (!$this->canStartTask($task)) {
            return false;
        }

        return DB::transaction(function () use ($task, $reason) {
            // Get first milestone
            $firstMilestone = $task->taskMilestones()
                ->orderBy('sequence_order', 'asc')
                ->first();

            if (!$firstMilestone) {
                Log::warning("Cannot start task {$task->id}: No milestones found");
                return false;
            }

            $task->update([
                'status' => 'in_progress',
                'actual_start_date' => now(),
                'current_milestone_id' => $firstMilestone->milestone_id,
                'early_start_reason' => $reason,
            ]);

            $firstMilestone->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            Log::info("Task {$task->id} started" . ($reason ? " (reason: {$reason})" : ""));

            return true;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function canStartTask(Task $task): bool
    {
        // Cannot start if already in progress or completed
        if (in_array($task->status, ['in_progress', 'completed', 'cancelled'])) {
            return false;
        }

        // Must have milestones
        if ($task->taskMilestones()->count() === 0) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function revertToMilestone(Task $task, TaskMilestone $milestone, User $user, string $reason): bool
    {
        if ($milestone->task_id !== $task->id) {
            return false;
        }

        return DB::transaction(function () use ($task, $milestone, $user, $reason) {
            // Reset milestones after this one
            $task->taskMilestones()
                ->where('sequence_order', '>', $milestone->sequence_order)
                ->update([
                    'status' => 'pending',
                    'started_at' => null,
                    'completed_at' => null,
                    'completed_by' => null,
                ]);

            // Set this milestone as current
            $task->update([
                'current_milestone_id' => $milestone->milestone_id,
                'status' => 'in_progress',
            ]);

            $milestone->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            Log::warning("Task {$task->id} reverted to milestone {$milestone->milestone_id} by user {$user->id}. Reason: {$reason}");

            return true;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function hasRequiredDocuments(Task $task): bool
    {
        if (!$task->current_milestone_id) {
            return true;
        }

        $currentMilestone = $task->currentMilestone;

        if (!$currentMilestone || !$currentMilestone->requires_docs) {
            return true;
        }

        // Check if task milestone has required documents
        $taskMilestone = $task->taskMilestones()
            ->where('milestone_id', $task->current_milestone_id)
            ->first();

        if (!$taskMilestone) {
            return false;
        }

        // Check if documents are attached (assuming you have a relationship)
        // You may need to adjust this based on your actual document attachment implementation
        $requiredDocs = $currentMilestone->required_documents ?? [];

        if (empty($requiredDocs)) {
            return true;
        }

        // This is a placeholder - implement based on your document attachment system
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getProgressionErrors(Task $task): array
    {
        $errors = [];

        // Check if task is in a valid state
        if (!in_array($task->status, ['draft', 'scheduled', 'in_progress'])) {
            $errors[] = "Task is in {$task->status} state and cannot progress";
        }

        // Check if there's a next milestone
        $nextMilestone = $this->getNextMilestone($task);
        if (!$nextMilestone && $task->current_milestone_id) {
            // This means we're at the last milestone, which is fine
            // Check if current milestone is ready to complete
            if (!$this->hasRequiredDocuments($task)) {
                $errors[] = 'Required documents are missing for current milestone';
            }
        }

        if (!$nextMilestone && !$task->current_milestone_id) {
            $errors[] = 'No milestones found for this task';
        }

        return $errors;
    }
}