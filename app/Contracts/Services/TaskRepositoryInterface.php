<?php

namespace App\Interfaces;

use App\Models\Task;
use App\Models\WorkflowTemplate;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function findById(int $id): ?Task;
    public function create(array $data): Task;
    public function update(Task $task, array $data): bool;
    public function delete(Task $task): bool;
    public function findByWorkflow(WorkflowTemplate $workflow): Collection;
    public function findActiveTasks(): Collection;
}