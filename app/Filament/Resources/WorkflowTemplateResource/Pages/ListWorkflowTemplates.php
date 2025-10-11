<?php

namespace App\Filament\Resources\WorkflowTemplateResource\Pages;

use App\Filament\Resources\WorkflowTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowTemplates extends ListRecords
{
    protected static string $resource = WorkflowTemplateResource::class;

    protected string $view = 'filament.resources.workflow-template-resource.pages.list-workflow-templates';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
