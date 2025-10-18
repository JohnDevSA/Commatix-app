<?php

namespace App\Events;

use App\Models\OnboardingProgress;
use App\Models\Tenant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * OnboardingCompleted Event
 *
 * Fired when a tenant completes the 6-step onboarding wizard.
 * Triggers post-onboarding automation via queued listeners.
 */
class OnboardingCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Tenant  $tenant  The tenant who completed onboarding
     * @param  OnboardingProgress  $progress  The onboarding progress record
     */
    public function __construct(
        public Tenant $tenant,
        public OnboardingProgress $progress
    ) {
        //
    }
}
