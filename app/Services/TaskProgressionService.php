<?php

namespace App\Services;

use App\Interfaces\TaskProgressionInterface;
use App\Models\Task;
use App\Models\Milestone;

class TaskProgressionService implements TaskProgressionInterface
{
    /**
     * Check if a task can be started
     */
    public function canStart(Task $task): bool
    {
        return $task->status === 'scheduled';
    }

    /**
     * Start a task and move it to the first milestone
     */
    public function start(Task $task, ?string $reason = null): bool
    {
        if (!$this->canStart($task)) {
            return false;
        }

        $firstMilestone = $task->workflowTemplate->milestones()
            ->orderBy('sequence_order')
            ->first();

        if (!$firstMilestone) {
            return false;
        }

        $task->update([
            'status' => 'in_progress',
            'actual_start_date' => now(),
            'current_milestone_id' => $firstMilestone->id,
            'early_start_reason' => $reason,
        ]);

        // Create task milestone record
        $task->taskMilestones()->create([
            'milestone_id' => $firstMilestone->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return true;
    }

    /**
     * Move task to the next milestone in the workflow
     */
    public function moveToNext(Task $task): bool
    {
        $currentMilestone = $task->currentMilestone;
        if (!$currentMilestone) {
            return false;
        }

        $nextMilestone = $task->workflowTemplate->milestones()
            ->where('sequence_order', '>', $currentMilestone->sequence_order)
            ->orderBy('sequence_order')
            ->first();

        if (!$nextMilestone) {
            // No next milestone, complete the task
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
                'current_milestone_id' => null,
            ]);
        } else {
            $task->update([
                'current_milestone_id' => $nextMilestone->id,
            ]);

            $task->taskMilestones()->create([
                'milestone_id' => $nextMilestone->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Check if task can start early
     */
    public function canStartEarly(Task $task): bool
    {
        return $task->status === 'scheduled' &&
            $task->scheduled_start_date > now()->toDateString();
    }

    /**
     * Check if task should auto-start
     */
    public function shouldAutoStart(Task $task): bool
    {
        return $task->status === 'scheduled' &&
            $task->scheduled_start_date <= now()->toDateString();
    }
}