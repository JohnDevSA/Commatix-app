<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * TenantProvisioningFailed Event
 *
 * Fired when tenant database provisioning fails.
 * Used to notify admins and log errors for debugging.
 */
class TenantProvisioningFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Tenant  $tenant  The tenant whose provisioning failed
     * @param  Throwable  $exception  The exception that caused the failure
     */
    public function __construct(
        public Tenant $tenant,
        public Throwable $exception
    ) {
        //
    }
}
