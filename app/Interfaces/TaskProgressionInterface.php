<?php

namespace App\Interfaces;

use App\Models\Task;
use App\Models\User;

interface TaskProgressionInterface
{
    public function canStart(Task $task): bool;
    public function start(Task $task, ?string $reason = null): bool;
    public function moveToNext(Task $task): bool;
    public function canStartEarly(Task $task): bool;
    public function shouldAutoStart(Task $task): bool;
}