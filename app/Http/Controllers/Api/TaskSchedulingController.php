<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\TaskSchedulingInterface;
use App\Contracts\Services\UserAssignmentStrategyInterface;
use App\Models\SubscriberList;
use App\Models\User;
use App\Services\UserAssignment\SingleUserAssignmentStrategy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskSchedulingController extends Controller
{
    public function __construct(
        private TaskSchedulingInterface $schedulingService
    ) {
    }

    /**
     * Schedule tasks for all subscribers in a list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function scheduleTasksForSubscribers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscriber_list_id' => 'required|exists:subscriber_lists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['nullable', Rule::in(['draft', 'scheduled', 'in_progress', 'completed', 'cancelled'])],
            'workflow_template_id' => 'nullable|exists:workflow_templates,id',
            'scheduled_start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:scheduled_start_date',
            'assignment_strategy' => ['nullable', Rule::in(['round_robin', 'single_user'])],
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscriberList = SubscriberList::findOrFail($request->subscriber_list_id);

            // Prepare task data
            $taskData = [
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => $request->status ?? 'draft',
                'workflow_template_id' => $request->workflow_template_id,
                'scheduled_start_date' => $request->scheduled_start_date ?? now(),
                'due_date' => $request->due_date,
            ];

            // Get users for assignment if provided
            $users = null;
            if ($request->user_ids) {
                $users = User::whereIn('id', $request->user_ids)->get();
            }

            // Set assignment strategy if needed
            if ($request->assignment_strategy === 'single_user' && $users && $users->isNotEmpty()) {
                $this->schedulingService->setAssignmentStrategy(
                    new SingleUserAssignmentStrategy($users->first())
                );
            }

            // Schedule tasks
            $tasks = $this->schedulingService->scheduleTasksForSubscribers(
                $subscriberList,
                $taskData,
                $users
            );

            return response()->json([
                'success' => true,
                'message' => "Successfully scheduled {$tasks->count()} tasks for subscriber list: {$subscriberList->name}",
                'data' => [
                    'tasks_created' => $tasks->count(),
                    'subscriber_list' => [
                        'id' => $subscriberList->id,
                        'name' => $subscriberList->name,
                    ],
                    'task_ids' => $tasks->pluck('id'),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scheduled tasks for a subscriber list
     *
     * @param int $subscriberListId
     * @return JsonResponse
     */
    public function getScheduledTasks(int $subscriberListId): JsonResponse
    {
        try {
            $subscriberList = SubscriberList::findOrFail($subscriberListId);

            $tasks = $subscriberList->subscribers()
                ->with('tasks.assignedTo', 'tasks.workflowTemplate')
                ->get()
                ->pluck('tasks')
                ->flatten();

            return response()->json([
                'success' => true,
                'data' => [
                    'subscriber_list' => [
                        'id' => $subscriberList->id,
                        'name' => $subscriberList->name,
                    ],
                    'total_tasks' => $tasks->count(),
                    'tasks' => $tasks->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'status' => $task->status,
                            'priority' => $task->priority,
                            'assigned_to' => $task->assignedTo?->name,
                            'scheduled_start_date' => $task->scheduled_start_date,
                            'due_date' => $task->due_date,
                        ];
                    }),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get task scheduling statistics
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $tenantId = auth()->user()->tenant_id;

            $stats = [
                'total_scheduled_tasks' => \App\Models\Task::where('tenant_id', $tenantId)
                    ->where('status', 'scheduled')
                    ->count(),
                'tasks_by_priority' => \App\Models\Task::where('tenant_id', $tenantId)
                    ->selectRaw('priority, count(*) as count')
                    ->groupBy('priority')
                    ->pluck('count', 'priority'),
                'tasks_by_status' => \App\Models\Task::where('tenant_id', $tenantId)
                    ->selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}