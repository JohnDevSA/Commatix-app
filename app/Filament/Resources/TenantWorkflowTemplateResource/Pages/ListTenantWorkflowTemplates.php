<?php

namespace App\Filament\Resources\TenantWorkflowTemplateResource\Pages;

use App\Filament\Resources\TenantWorkflowTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantWorkflowTemplates extends ListRecords
{
    protected static string $resource = TenantWorkflowTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Custom Workflow'),
        ];
    }
}
