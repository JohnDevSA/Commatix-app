<?php

namespace App\Http\Controllers;

use App\Interfaces\TaskSchedulingInterface;
use App\Interfaces\UserAssignmentStrategyInterface;
use App\Services\UserAssignment\RoundRobinAssignmentStrategy;
use App\Services\UserAssignment\SingleUserAssignmentStrategy;
use App\Models\SubscriberList;
use App\Models\User;
use Illuminate\Http\Request;

class TaskSchedulingController extends Controller
{
    private TaskSchedulingInterface $taskSchedulingService;

    public function __construct(TaskSchedulingInterface $taskSchedulingService)
    {
        $this->taskSchedulingService = $taskSchedulingService;
    }

    /**
     * Schedule tasks for subscribers in a list
     */
    public function scheduleTasks(Request $request)
    {
        $request->validate([
            'subscriber_list_id' => 'required|exists:subscriber_lists,id',
            'task_data' => 'required|array',
            'assignment_strategy' => 'required|in:single,round-robin',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $subscriberList = SubscriberList::findOrFail($request->subscriber_list_id);
        
        // Get users for assignment
        $users = collect();
        if ($request->assignment_strategy === 'single' && $request->assigned_user_id) {
            $users = collect([User::findOrFail($request->assigned_user_id)]);
        } elseif ($request->assignment_strategy === 'round-robin') {
            // Get all users from the tenant
            $users = User::where('tenant_id', $subscriberList->tenant_id)->get();
        }

        // Schedule tasks
        $tasks = $this->taskSchedulingService->scheduleTasksForSubscribers(
            $subscriberList,
            $request->task_data,
            $users
        );

        return response()->json([
            'message' => 'Tasks scheduled successfully',
            'tasks_created' => $tasks->count(),
            'tasks' => $tasks
        ]);
    }
}