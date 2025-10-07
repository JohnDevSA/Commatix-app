<?php

namespace App\Interfaces;

use App\Models\WorkflowTemplate;
use App\Models\Milestone;
use Illuminate\Support\Collection;

interface WorkflowMilestoneInterface
{
    public function addMilestone(WorkflowTemplate $workflow, array $milestoneData): Milestone;
    public function removeMilestone(WorkflowTemplate $workflow, Milestone $milestone): bool;
    public function getMilestones(WorkflowTemplate $workflow): Collection;
    public function markMilestonesComplete(WorkflowTemplate $workflow): void;
}