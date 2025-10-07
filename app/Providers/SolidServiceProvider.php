<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\WorkflowLockingInterface;
use App\Interfaces\TaskProgressionInterface;
use App\Interfaces\WorkflowRepositoryInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Services\WorkflowLockService;
use App\Services\TaskProgressionService;
use App\Repositories\WorkflowRepository;
use App\Repositories\TaskRepository;

class SolidServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(
            WorkflowLockingInterface::class,
            WorkflowLockService::class
        );

        $this->app->bind(
            TaskProgressionInterface::class,
            TaskProgressionService::class
        );

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