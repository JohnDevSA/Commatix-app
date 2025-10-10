<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\UserType;
use App\Models\WorkflowTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmupCache extends Command
{
    protected $signature = 'cache:warmup';

    protected $description = 'Warm up application caches for better performance';

    public function handle(): int
    {
        $this->info('Warming up application caches...');

        // Warm up user types cache
        $this->info('Caching user types...');
        Cache::forget('user_types_all');
        Cache::remember('user_types_all', 14400, function () {
            return UserType::all();
        });

        // Warm up workflow templates cache
        $this->info('Caching workflow templates...');
        Cache::forget('workflow_templates_published');
        Cache::remember('workflow_templates_published', 7200, function () {
            return WorkflowTemplate::where('is_published', true)
                ->with(['milestones'])
                ->get();
        });

        // Warm up tenant-specific caches
        $this->info('Caching tenant data...');
        $tenants = Tenant::all();

        $progressBar = $this->output->createProgressBar($tenants->count());
        $progressBar->start();

        foreach ($tenants as $tenant) {
            // Use string-based UUID tenant ID
            $tenantId = $tenant->id;

            Cache::forget("tenant_data_{$tenantId}");
            Cache::forget("document_types_{$tenantId}");
            Cache::forget("tenant_divisions_{$tenantId}");
            Cache::forget("tenant_users_{$tenantId}");

            Cache::remember("tenant_data_{$tenantId}", 3600, function () use ($tenant) {
                return Tenant::with(['divisions', 'users.userType'])->find($tenant->id);
            });

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info('Cache warmup completed successfully!');

        return 0;
    }
}
