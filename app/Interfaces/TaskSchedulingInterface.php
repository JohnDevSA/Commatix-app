<?php

namespace App\Interfaces;

use App\Models\SubscriberList;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskSchedulingInterface
{
    public function scheduleTasksForSubscribers(
        SubscriberList $subscriberList, 
        array $taskData,
        ?Collection $users = null
    ): Collection;
    
    public function assignTaskToUser(Task $task, Collection $users): User;
}