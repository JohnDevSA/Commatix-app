<?php

namespace App\Interfaces;

use App\Models\User;
use App\Models\WorkflowTemplate;

interface WorkflowLockingInterface
{
    public function lock(WorkflowTemplate $workflow, User $user, string $reason): void;
    public function unlock(WorkflowTemplate $workflow): void;
    public function isLockedBy(WorkflowTemplate $workflow, User $user): bool;
    public function canBeEditedBy(WorkflowTemplate $workflow, User $user): bool;
}