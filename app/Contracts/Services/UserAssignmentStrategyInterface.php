<?php

namespace App\Interfaces;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

interface UserAssignmentStrategyInterface
{
    public function assignUser(Task $task, Collection $users): User;
}