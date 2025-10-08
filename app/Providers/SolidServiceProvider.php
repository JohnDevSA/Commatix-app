<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces from consolidated location
use App\Contracts\Services\AuthorizationServiceInterface;
use App\Contracts\Services\WorkflowLockingInterface;
use App\Contracts\Services\TaskProgressionInterface;
use App\Contracts\Services\WorkflowRepositoryInterface;
use App\Contracts\Services\TaskRepositoryInterface;
use App\Contracts\Services\TaskSchedulingInterface;
use App\Contracts\Services\UserAssignmentStrategyInterface;

// Service implementations from organized directories
use App\Services\Authorization\AuthorizationService;
use App\Services\Workflow\WorkflowLockService;
use App\Services\Task\TaskProgressionService;
use App\Services\TaskSchedulingService;
use App\Services\UserAssignment\RoundRobinAssignmentStrategy;

// Repository implementations
use App\Repositories\WorkflowRepository;
use App\Repositories\TaskRepository;

class SolidServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Authorization Service - available for future use
        $this->app->singleton(
            AuthorizationServiceInterface::class,
            AuthorizationService::class
        );

        // Workflow Locking Service - actively used
        $this->app->singleton(
            WorkflowLockingInterface::class,
            WorkflowLockService::class
        );

        // Task Progression Service - actively used
        $this->app->singleton(
            TaskProgressionInterface::class,
            TaskProgressionService::class
        );

        // Task Scheduling Service - actively used (pre-existing)
        $this->app->singleton(
            TaskSchedulingInterface::class,
            TaskSchedulingService::class
        );

        // User Assignment Strategy - actively used
        $this->app->bind(
            UserAssignmentStrategyInterface::class,
            RoundRobinAssignmentStrategy::class
        );

        // Repository Pattern (pre-existing)
        $this->app->bind(
            WorkflowRepositoryInterface::class,
            WorkflowRepository::class
        );

        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
