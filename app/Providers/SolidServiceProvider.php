<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces from consolidated location
use App\Contracts\Services\AuthorizationServiceInterface;
use App\Contracts\Services\WorkflowLockingInterface;
use App\Contracts\Services\TaskProgressionInterface;
use App\Contracts\Services\TaskSchedulingInterface;
use App\Contracts\Services\UserAssignmentStrategyInterface;
use App\Contracts\Services\CreditManagementInterface;

// Service implementations from organized directories
use App\Services\Authorization\AuthorizationService;
use App\Services\Workflow\WorkflowLockService;
use App\Services\Task\TaskProgressionService;
use App\Services\Task\TaskSchedulingService;
use App\Services\UserAssignment\RoundRobinAssignmentStrategy;
use App\Services\Billing\CreditManagementService;

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

        // Task Scheduling Service - actively used for auto-scheduling
        $this->app->singleton(
            TaskSchedulingInterface::class,
            TaskSchedulingService::class
        );

        // User Assignment Strategy - Round Robin by default
        $this->app->bind(
            UserAssignmentStrategyInterface::class,
            RoundRobinAssignmentStrategy::class
        );

        // Credit Management Service - manages tenant communication credits
        $this->app->singleton(
            CreditManagementInterface::class,
            CreditManagementService::class
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
