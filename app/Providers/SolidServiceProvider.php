<?php

namespace App\Providers;

use App\Contracts\Services\AuthorizationServiceInterface;
use App\Contracts\Services\TaskProgressionInterface;
use App\Contracts\Services\WorkflowLockingInterface;
use App\Services\Authorization\AuthorizationService;
use App\Services\Task\TaskProgressionService;
use App\Services\Workflow\WorkflowLockService;
use Illuminate\Support\ServiceProvider;

/**
 * SOLID Service Provider
 *
 * Registers all service implementations following SOLID principles.
 * This provider binds interfaces to their concrete implementations,
 * enabling Dependency Inversion and making the code easily testable and maintainable.
 *
 * @package App\Providers
 */
class SolidServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * Using singletons for these services ensures consistent state across the application
     * and improves performance by avoiding repeated instantiation.
     *
     * @var array
     */
    public $singletons = [
        AuthorizationServiceInterface::class => AuthorizationService::class,
        WorkflowLockingInterface::class => WorkflowLockService::class,
        TaskProgressionInterface::class => TaskProgressionService::class,
    ];

    /**
     * Register services.
     *
     * Services are automatically registered as singletons using the $singletons property.
     * This follows the Dependency Inversion Principle by depending on abstractions (interfaces)
     * rather than concrete implementations.
     *
     * @return void
     */
    public function register(): void
    {
        // Singletons are automatically registered by Laravel
        // using the $singletons property above
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}