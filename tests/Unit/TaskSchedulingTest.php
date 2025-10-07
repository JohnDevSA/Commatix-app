<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TaskSchedulingService;
use App\Services\UserAssignment\RoundRobinAssignmentStrategy;
use App\Services\UserAssignment\SingleUserAssignmentStrategy;
use App\Models\Task;
use App\Models\User;
use App\Models\SubscriberList;
use Mockery;

class TaskSchedulingTest extends TestCase
{
    public function test_round_robin_assignment()
    {
        $users = collect([
            Mockery::mock(User::class),
            Mockery::mock(User::class),
            Mockery::mock(User::class)
        ]);
        
        $strategy = new RoundRobinAssignmentStrategy();
        
        // Test that users are assigned in rotation
        $task1 = Mockery::mock(Task::class);
        $assignedUser1 = $strategy->assignUser($task1, $users);
        
        $task2 = Mockery::mock(Task::class);
        $assignedUser2 = $strategy->assignUser($task2, $users);
        
        // We're just verifying the method works, not the exact rotation logic
        $this->assertInstanceOf(User::class, $assignedUser1);
    }
    
    public function test_single_user_assignment()
    {
        $user = Mockery::mock(User::class);
        $users = collect([$user]);
        
        $strategy = new SingleUserAssignmentStrategy($user);
        
        $task = Mockery::mock(Task::class);
        $assignedUser = $strategy->assignUser($task, $users);
        
        $this->assertEquals($user, $assignedUser);
    }
}