<?php

namespace App\Jobs;

use App\Events\TenantDatabaseCreated;
use App\Events\TenantProvisioningFailed;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * CreateTenantDatabaseJob
 *
 * Asynchronously creates and provisions a tenant database.
 * This job runs on the 'tenant-provisioning' queue with retry logic.
 *
 * Flow:
 * 1. Update status to 'provisioning'
 * 2. Create tenant database via stancl/tenancy
 * 3. Run tenant migrations
 * 4. Update status to 'ready'
 * 5. Fire TenantDatabaseCreated event
 *
 * On failure:
 * - Retries 3 times with exponential backoff
 * - Fires TenantProvisioningFailed event
 * - Logs error for admin review
 */
class CreateTenantDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 120, 300]; // 1min, 2min, 5min exponential backoff
    }

    /**
     * Create a new job instance.
     *
     * @param  Tenant  $tenant  The tenant to provision
     */
    public function __construct(
        public Tenant $tenant
    ) {
        // Queue this job on dedicated tenant-provisioning queue
        $this->onQueue('tenant-provisioning');
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        Log::info('Starting tenant database provisioning', [
            'tenant_id' => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
        ]);

        try {
            // Step 1: Update status to provisioning
            $this->updateTenantStatus('provisioning');

            // Step 2: Create tenant database using stancl/tenancy
            $this->createTenantDatabase();

            // Step 3: Run tenant migrations
            $this->runTenantMigrations();

            // Step 4: Update status to ready
            $this->updateTenantStatus('ready');

            // Step 5: Fire success event
            event(new TenantDatabaseCreated($this->tenant));

            Log::info('Tenant database provisioning completed successfully', [
                'tenant_id' => $this->tenant->id,
            ]);
        } catch (Throwable $e) {
            // Log the error
            Log::error('Tenant database provisioning failed', [
                'tenant_id' => $this->tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update tenant status to indicate failure
            $this->tenant->update([
                'onboarding_status' => 'abandoned',
            ]);

            // Fire failure event (for admin notifications)
            event(new TenantProvisioningFailed($this->tenant, $e));

            // Re-throw to trigger job retry mechanism
            throw $e;
        }
    }

    /**
     * Update tenant onboarding status.
     */
    protected function updateTenantStatus(string $status): void
    {
        $this->tenant->update([
            'onboarding_status' => $status,
        ]);

        $this->tenant->refresh();
    }

    /**
     * Create the tenant database.
     *
     * Uses stancl/tenancy package to create database.
     */
    protected function createTenantDatabase(): void
    {
        // stancl/tenancy automatically creates database when tenant is created
        // But we can manually trigger it if needed
        if (! $this->tenant->database()->exists()) {
            $this->tenant->database()->makeDatabase();

            Log::info('Tenant database created', [
                'tenant_id' => $this->tenant->id,
                'database_name' => $this->tenant->database()->getName(),
            ]);
        }
    }

    /**
     * Run migrations on the tenant database.
     */
    protected function runTenantMigrations(): void
    {
        // Run migrations specifically for this tenant
        Artisan::call('tenants:migrate', [
            '--tenants' => [$this->tenant->id],
        ]);

        $output = Artisan::output();

        Log::info('Tenant migrations completed', [
            'tenant_id' => $this->tenant->id,
            'output' => $output,
        ]);
    }

    /**
     * Handle a job failure.
     *
     * Called when all retry attempts have been exhausted.
     */
    public function failed(Throwable $exception): void
    {
        Log::critical('Tenant database provisioning permanently failed after all retries', [
            'tenant_id' => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'error' => $exception->getMessage(),
        ]);

        // Mark tenant as abandoned
        $this->tenant->update([
            'onboarding_status' => 'abandoned',
        ]);

        // Fire failure event for admin notification
        event(new TenantProvisioningFailed($this->tenant, $exception));

        // TODO: Send notification to admin team
        // Notification::route('mail', config('mail.admin_email'))
        //     ->notify(new TenantProvisioningFailedNotification($this->tenant, $exception));
    }
}
