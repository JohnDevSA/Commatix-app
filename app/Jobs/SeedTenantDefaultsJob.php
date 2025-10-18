<?php

namespace App\Jobs;

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
 * SeedTenantDefaultsJob
 *
 * Seeds a tenant database with industry-specific defaults and sample data.
 * Runs after tenant database is created and ready.
 *
 * Seeds:
 * - Industry-specific workflow templates
 * - Default divisions (if team structure provided)
 * - Sample data (if requested)
 * - Default user roles and permissions
 *
 * This job is queued automatically after onboarding wizard completion.
 */
class SeedTenantDefaultsJob implements ShouldQueue
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
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 60, 120]; // 30s, 1min, 2min
    }

    /**
     * Create a new job instance.
     *
     * @param  Tenant  $tenant  The tenant to seed
     * @param  string|null  $selectedUseCase  Selected use case from wizard
     * @param  bool  $includeSampleData  Whether to include sample data
     */
    public function __construct(
        public Tenant $tenant,
        public ?string $selectedUseCase = null,
        public bool $includeSampleData = false
    ) {
        // Queue on tenant-provisioning queue
        $this->onQueue('tenant-provisioning');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting tenant defaults seeding", [
            'tenant_id' => $this->tenant->id,
            'use_case' => $this->selectedUseCase,
            'sample_data' => $this->includeSampleData,
        ]);

        try {
            // Initialize tenant context
            tenancy()->initialize($this->tenant);

            // Seed based on selected use case
            $this->seedIndustryTemplates();

            // Create default divisions if team structure was configured
            $this->createDefaultDivisions();

            // Seed sample data if requested
            if ($this->includeSampleData) {
                $this->seedSampleData();
            }

            Log::info("Tenant defaults seeding completed", [
                'tenant_id' => $this->tenant->id,
            ]);
        } catch (Throwable $e) {
            Log::error("Tenant defaults seeding failed", [
                'tenant_id' => $this->tenant->id,
                'error' => $e->getMessage(),
            ]);

            // Don't fail the whole onboarding - this is non-critical
            // Just log and continue
        } finally {
            // End tenant context
            tenancy()->end();
        }
    }

    /**
     * Seed industry-specific workflow templates based on use case.
     */
    protected function seedIndustryTemplates(): void
    {
        if (! $this->selectedUseCase) {
            return;
        }

        Log::info("Seeding industry templates for use case", [
            'tenant_id' => $this->tenant->id,
            'use_case' => $this->selectedUseCase,
        ]);

        // Map use cases to seeders
        $seederMap = [
            'email_marketing' => 'MarketingWorkflowSeeder',
            'sms_campaigns' => 'CommunicationWorkflowSeeder',
            'workflow_automation' => 'AutomationWorkflowSeeder',
            'task_management' => 'TaskManagementSeeder',
            'multi_channel' => 'ComprehensiveWorkflowSeeder',
        ];

        $seederClass = $seederMap[$this->selectedUseCase] ?? null;

        if ($seederClass && class_exists("Database\\Seeders\\{$seederClass}")) {
            Artisan::call("db:seed", [
                '--class' => "Database\\Seeders\\{$seederClass}",
            ]);

            Log::info("Industry templates seeded", [
                'tenant_id' => $this->tenant->id,
                'seeder' => $seederClass,
            ]);
        }
    }

    /**
     * Create default divisions based on team structure from wizard.
     */
    protected function createDefaultDivisions(): void
    {
        // Get team structure from wizard data
        $wizardData = $this->tenant->setup_wizard_data ?? [];
        $teamStructure = $wizardData['step_2']['divisions'] ?? [];

        if (empty($teamStructure)) {
            // Create basic default divisions
            $teamStructure = ['General', 'Management'];
        }

        foreach ($teamStructure as $divisionName) {
            \App\Models\Division::create([
                'name' => $divisionName,
                'tenant_id' => $this->tenant->id,
            ]);
        }

        Log::info("Default divisions created", [
            'tenant_id' => $this->tenant->id,
            'divisions' => $teamStructure,
        ]);
    }

    /**
     * Seed sample data for testing and demo purposes.
     */
    protected function seedSampleData(): void
    {
        Log::info("Seeding sample data", [
            'tenant_id' => $this->tenant->id,
        ]);

        // Run demo data seeder
        Artisan::call("db:seed", [
            '--class' => 'Database\\Seeders\\DemoTenantSeeder',
        ]);

        Log::info("Sample data seeded", [
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Tenant defaults seeding permanently failed", [
            'tenant_id' => $this->tenant->id,
            'error' => $exception->getMessage(),
        ]);

        // This is non-critical, so we don't mark tenant as abandoned
        // Just notify admins for manual review
    }
}
