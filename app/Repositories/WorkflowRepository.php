<?php

namespace App\Repositories;

use App\Interfaces\WorkflowRepositoryInterface;
use App\Models\WorkflowTemplate;
use App\Models\Milestone;
use Illuminate\Support\Collection;

class WorkflowRepository implements WorkflowRepositoryInterface
{
    /**
     * Find a workflow by ID
     */
    public function findById(int $id): ?WorkflowTemplate
    {
        return WorkflowTemplate::find($id);
    }

    /**
     * Create a new workflow
     */
    public function create(array $data): WorkflowTemplate
    {
        return WorkflowTemplate::create($data);
    }

    /**
     * Update a workflow
     */
    public function update(WorkflowTemplate $workflow, array $data): bool
    {
        return $workflow->update($data);
    }

    /**
     * Delete a workflow
     */
    public function delete(WorkflowTemplate $workflow): bool
    {
        return $workflow->delete();
    }

    /**
     * Get all milestones for a workflow
     */
    public function findMilestones(WorkflowTemplate $workflow): Collection
    {
        return $workflow->milestones()->orderBy('sequence_order')->get();
    }

    /**
     * Find a milestone by ID
     */
    public function findMilestoneById(int $id): ?Milestone
    {
        return Milestone::find($id);
    }
}