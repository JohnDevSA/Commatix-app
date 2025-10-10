<?php

namespace App\Services\Workflow;

use App\Contracts\Services\WorkflowLockingInterface;
use App\Models\Milestone;
use App\Models\User;
use App\Models\WorkflowTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Workflow Lock Service
 *
 * Manages the locking and unlocking of workflow milestones.
 * This service ensures that critical milestones in system and industry templates
 * cannot be modified by tenants, maintaining compliance and standardization.
 */
class WorkflowLockService implements WorkflowLockingInterface
{
    /**
     * Cache key prefix for locked milestones
     */
    private const CACHE_PREFIX = 'workflow_locks:';

    /**
     * Cache TTL in seconds (24 hours)
     */
    private const CACHE_TTL = 86400;

    /**
     * {@inheritDoc}
     */
    public function lockMilestone(Milestone $milestone): void
    {
        // Update the locked_milestones JSON in the workflow template
        $workflow = $milestone->workflowTemplate;

        if (! $workflow) {
            Log::warning("Attempted to lock milestone {$milestone->id} without associated workflow");

            return;
        }

        $lockedMilestones = $this->getLockedMilestones($workflow);

        if (! in_array($milestone->id, $lockedMilestones)) {
            $lockedMilestones[] = $milestone->id;
            $workflow->locked_milestones = json_encode($lockedMilestones);
            $workflow->save();

            // Clear cache
            $this->clearCache($workflow);

            Log::info("Locked milestone {$milestone->id} for workflow {$workflow->id}");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unlockMilestone(Milestone $milestone): void
    {
        $workflow = $milestone->workflowTemplate;

        if (! $workflow) {
            Log::warning("Attempted to unlock milestone {$milestone->id} without associated workflow");

            return;
        }

        $lockedMilestones = $this->getLockedMilestones($workflow);
        $lockedMilestones = array_filter($lockedMilestones, fn ($id) => $id !== $milestone->id);

        $workflow->locked_milestones = json_encode(array_values($lockedMilestones));
        $workflow->save();

        // Clear cache
        $this->clearCache($workflow);

        Log::info("Unlocked milestone {$milestone->id} for workflow {$workflow->id}");
    }

    /**
     * {@inheritDoc}
     */
    public function lockMilestones(WorkflowTemplate $workflow, array $milestoneIds): void
    {
        $currentLocked = $this->getLockedMilestones($workflow);
        $newLocked = array_unique(array_merge($currentLocked, $milestoneIds));

        $workflow->locked_milestones = json_encode($newLocked);
        $workflow->save();

        // Clear cache
        $this->clearCache($workflow);

        Log::info('Locked '.count($milestoneIds)." milestones for workflow {$workflow->id}");
    }

    /**
     * {@inheritDoc}
     */
    public function unlockMilestones(WorkflowTemplate $workflow, array $milestoneIds): void
    {
        $currentLocked = $this->getLockedMilestones($workflow);
        $newLocked = array_filter($currentLocked, fn ($id) => ! in_array($id, $milestoneIds));

        $workflow->locked_milestones = json_encode(array_values($newLocked));
        $workflow->save();

        // Clear cache
        $this->clearCache($workflow);

        Log::info('Unlocked '.count($milestoneIds)." milestones for workflow {$workflow->id}");
    }

    /**
     * {@inheritDoc}
     */
    public function isLocked(Milestone $milestone): bool
    {
        $workflow = $milestone->workflowTemplate;

        if (! $workflow) {
            return false;
        }

        $lockedMilestones = $this->getCachedLockedMilestones($workflow);

        return in_array($milestone->id, $lockedMilestones);
    }

    /**
     * {@inheritDoc}
     */
    public function canModify(Milestone $milestone, User $user): bool
    {
        // Super admins can modify anything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // If milestone is locked, only super admins can modify
        if ($this->isLocked($milestone)) {
            return false;
        }

        // Check if user owns the workflow
        $workflow = $milestone->workflowTemplate;

        if (! $workflow) {
            return false;
        }

        // System templates cannot be modified by non-super admins
        if ($workflow->is_system_template) {
            return false;
        }

        // User must own the workflow to modify milestones
        return $workflow->created_by === $user->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getLockedMilestones(WorkflowTemplate $workflow): array
    {
        if (empty($workflow->locked_milestones)) {
            return [];
        }

        $locked = json_decode($workflow->locked_milestones, true);

        return is_array($locked) ? $locked : [];
    }

    /**
     * {@inheritDoc}
     */
    public function lockSystemTemplate(WorkflowTemplate $workflow): void
    {
        if (! $workflow->is_system_template) {
            Log::warning("Attempted to lock non-system template {$workflow->id} as system template");

            return;
        }

        // Get all milestone IDs for this workflow
        $milestoneIds = $workflow->milestones()->pluck('id')->toArray();

        if (empty($milestoneIds)) {
            Log::warning("No milestones found for system template {$workflow->id}");

            return;
        }

        $this->lockMilestones($workflow, $milestoneIds);

        Log::info("Locked all milestones for system template {$workflow->id}");
    }

    /**
     * Get cached locked milestones for a workflow.
     */
    private function getCachedLockedMilestones(WorkflowTemplate $workflow): array
    {
        $cacheKey = self::CACHE_PREFIX.$workflow->id;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($workflow) {
            return $this->getLockedMilestones($workflow);
        });
    }

    /**
     * Clear the cache for a workflow's locked milestones.
     */
    private function clearCache(WorkflowTemplate $workflow): void
    {
        $cacheKey = self::CACHE_PREFIX.$workflow->id;
        Cache::forget($cacheKey);
    }
}
