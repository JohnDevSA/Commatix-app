<?php

namespace App\Filament\Resources\TenantWorkflowTemplateResource\Pages;

use App\Filament\Resources\TenantWorkflowTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTenantWorkflowTemplate extends ViewRecord
{
    protected static string $resource = TenantWorkflowTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->created_by === auth()->id()),
        ];
    }
}
