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
                ->label('Create Custom Workflow')
                ->icon('heroicon-o-plus-circle')
                ->color('primary'),
        ];
    }

    public function getEmptyStateIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public function getEmptyStateHeading(): ?string
    {
        return 'No workflow templates yet';
    }

    public function getEmptyStateDescription(): ?string
    {
        return 'Get started by creating your first custom workflow template, or browse industry-standard templates to adapt to your needs.';
    }

    public function getEmptyStateActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Your First Workflow')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->button(),

            Actions\Action::make('browse_templates')
                ->label('Browse Template Library')
                ->icon('heroicon-o-book-open')
                ->color('gray')
                ->url(fn () => static::getUrl())
                ->button()
                ->outlined(),
        ];
    }

    /**
     * Get custom empty state view
     *
     * Override to use our beautiful Commatix design system empty state
     */
    public function getEmptyStateView(): ?string
    {
        return 'filament.pages.workflows.empty-state';
    }
}
