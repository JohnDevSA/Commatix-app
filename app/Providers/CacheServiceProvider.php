<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Tenant;
use App\Models\DocumentType;
use App\Models\WorkflowTemplate;
use App\Models\UserType;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Cache tenant data for 1 hour
        $this->cacheTenantData();

        // Cache user types for 4 hours (rarely changed)
        $this->cacheUserTypes();

        // Cache workflow templates for 2 hours
        $this->cacheWorkflowTemplates();

        // Cache document types for 1 hour
        $this->cacheDocumentTypes();
    }

    protected function cacheTenantData(): void
    {
        if (!app()->runningInConsole()) {
            $tenantId = tenant('id');
            if ($tenantId) {
                Cache::remember("tenant_data_{$tenantId}", 3600, function () use ($tenantId) {
                    return Tenant::with(['divisions', 'users.userType'])->find($tenantId);
                });
            }
        }
    }

    protected function cacheUserTypes(): void
    {
        Cache::remember('user_types_all', 14400, function () {
            return UserType::all();
        });
    }

    protected function cacheWorkflowTemplates(): void
    {
        if (!app()->runningInConsole()) {
            Cache::remember('workflow_templates_published', 7200, function () {
                return WorkflowTemplate::where('is_published', true)
                    ->with(['milestones', 'tags'])
                    ->get();
            });
        }
    }

    protected function cacheDocumentTypes(): void
    {
        if (!app()->runningInConsole()) {
            $tenantId = tenant('id');
            if ($tenantId) {
                Cache::remember("document_types_{$tenantId}", 3600, function () use ($tenantId) {
                    return DocumentType::with(['divisions', 'workflowTemplate'])
                        ->where('tenant_id', $tenantId)
                        ->get();
                });
            }
        }
    }
}
