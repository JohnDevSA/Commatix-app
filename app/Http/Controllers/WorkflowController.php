<?php

namespace App\Http\Controllers;

use App\Interfaces\WorkflowLockingInterface;
use App\Interfaces\TaskProgressionInterface;
use App\Interfaces\WorkflowRepositoryInterface;
use App\Models\WorkflowTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    private WorkflowLockingInterface $workflowLockingService;
    private TaskProgressionInterface $taskProgressionService;
    private WorkflowRepositoryInterface $workflowRepository;

    public function __construct(
        WorkflowLockingInterface $workflowLockingService,
        TaskProgressionInterface $taskProgressionService,
        WorkflowRepositoryInterface $workflowRepository
    ) {
        $this->workflowLockingService = $workflowLockingService;
        $this->taskProgressionService = $taskProgressionService;
        $this->workflowRepository = $workflowRepository;
    }

    /**
     * Lock a workflow for editing
     */
    public function lockWorkflow(Request $request, int $workflowId)
    {
        $workflow = $this->workflowRepository->findById($workflowId);
        if (!$workflow) {
            return response()->json(['error' => 'Workflow not found'], 404);
        }

        /** @var User $user */
        $user = $request->user();
        
        $this->workflowLockingService->lock($workflow, $user, $request->reason ?? 'Configuring milestones');
        
        return response()->json(['message' => 'Workflow locked successfully']);
    }

    /**
     * Unlock a workflow
     */
    public function unlockWorkflow(int $workflowId)
    {
        $workflow = $this->workflowRepository->findById($workflowId);
        if (!$workflow) {
            return response()->json(['error' => 'Workflow not found'], 404);
        }

        $this->workflowLockingService->unlock($workflow);
        
        return response()->json(['message' => 'Workflow unlocked successfully']);
    }

    /**
     * Start a task
     */
    public function startTask(Request $request, int $taskId)
    {
        // In a real implementation, you would fetch the task through a TaskRepository
        // This is just an example of how to use the service
        return response()->json(['message' => 'Task start logic would be implemented here']);
    }
}