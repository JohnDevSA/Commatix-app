<?php

namespace App\Providers;

use App\Contracts\Services\AuthorizationServiceInterface;
use App\Contracts\Services\CampaignServiceInterface;
// Interfaces from consolidated location
use App\Contracts\Services\CreditManagementInterface;
use App\Contracts\Services\TaskProgressionInterface;
use App\Contracts\Services\TaskSchedulingInterface;
use App\Contracts\Services\TemplateRendererInterface;
use App\Contracts\Services\UserAssignmentStrategyInterface;
use App\Contracts\Services\WorkflowLockingInterface;
use App\Services\Authorization\AuthorizationService;
// Service implementations from organized directories
use App\Services\Billing\CreditManagementService;
use App\Services\Communication\CampaignService;
use App\Services\Communication\Senders\EmailSenderService;
use App\Services\Communication\Senders\SmsSenderService;
use App\Services\Communication\Senders\WhatsAppSenderService;
use App\Services\Communication\TemplateRendererService;
use App\Services\Task\TaskProgressionService;
use App\Services\Task\TaskSchedulingService;
use App\Services\UserAssignment\RoundRobinAssignmentStrategy;
use App\Services\Workflow\WorkflowLockService;
use Illuminate\Support\ServiceProvider;

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

        // Campaign Service - manages communication campaigns
        $this->app->singleton(
            CampaignServiceInterface::class,
            CampaignService::class
        );

        // Template Renderer Service - renders message templates
        $this->app->singleton(
            TemplateRendererInterface::class,
            TemplateRendererService::class
        );

        // Message Sender Services - channel-specific senders
        $this->app->singleton('message_sender.email', EmailSenderService::class);
        $this->app->singleton('message_sender.sms', SmsSenderService::class);
        $this->app->singleton('message_sender.whatsapp', WhatsAppSenderService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
