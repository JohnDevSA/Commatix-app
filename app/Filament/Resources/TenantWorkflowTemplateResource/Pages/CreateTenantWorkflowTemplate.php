<?php

namespace App\Filament\Resources\TenantWorkflowTemplateResource\Pages;

use App\Filament\Resources\TenantWorkflowTemplateResource;
use App\Models\AccessScope;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTenantWorkflowTemplate extends CreateRecord
{
    protected static string $resource = TenantWorkflowTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function beforeCreate(): void
    {
        // Set default values for tenant workflows
        $this->data['template_type'] = 'custom';
        $this->data['created_by'] = auth()->id();
        $this->data['user_id'] = auth()->id();
        $this->data['is_public'] = false;
        $this->data['is_published'] = false;
        $this->data['is_system_template'] = false;
        $this->data['template_version'] = '1.0';
        $this->data['complexity_level'] = $this->data['complexity_level'] ?? 'simple';

        // Set tenant_id if in tenant context
        if (tenant()) {
            $this->data['tenant_id'] = tenant()->id;
        }

        // Ensure access_scope_id is set if not provided
        if (empty($this->data['access_scope_id'])) {
            $this->data['access_scope_id'] = AccessScope::where('name', 'tenant_custom')->first()?->id;
        }

        // Set estimated_duration_days default
        if (empty($this->data['estimated_duration_days'])) {
            $this->data['estimated_duration_days'] = 0;
        }
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Workflow Created')
            ->body('Your custom workflow has been created. Add milestones to complete setup.')
            ->success()
            ->send();
    }
}
