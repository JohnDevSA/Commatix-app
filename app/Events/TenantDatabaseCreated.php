<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TenantDatabaseCreated Event
 *
 * Fired when a tenant's database has been successfully created and migrated.
 * This signals that the tenant is ready to begin the onboarding wizard.
 */
class TenantDatabaseCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Tenant  $tenant  The tenant whose database was created
     */
    public function __construct(
        public Tenant $tenant
    ) {
        //
    }
}
