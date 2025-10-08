<?php

namespace App\Interfaces;

use App\Models\WorkflowTemplate;
use App\Models\Milestone;
use Illuminate\Support\Collection;

interface WorkflowRepositoryInterface
{
    public function findById(int $id): ?WorkflowTemplate;
    public function create(array $data): WorkflowTemplate;
    public function update(WorkflowTemplate $workflow, array $data): bool;
    public function delete(WorkflowTemplate $workflow): bool;
    public function findMilestones(WorkflowTemplate $workflow): Collection;
    public function findMilestoneById(int $id): ?Milestone;
}