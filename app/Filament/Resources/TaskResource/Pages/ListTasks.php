<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected string $view = 'filament.resources.task-resource.pages.list-tasks';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Task')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }

    /**
     * Empty state configuration
     */
    public function getEmptyStateIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public function getEmptyStateHeading(): ?string
    {
        return 'Your Task List is Empty';
    }

    public function getEmptyStateDescription(): ?string
    {
        return 'Tasks are automatically created from workflow templates. Start a workflow or create a standalone task to get organized.';
    }

    public function getEmptyStateActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create a Task')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->button(),

            Actions\Action::make('browse_workflows')
                ->label('Browse Workflows')
                ->icon('heroicon-o-squares-2x2')
                ->color('gray')
                ->url(route('filament.app.resources.tenant-workflow-templates.index'))
                ->button()
                ->outlined(),
        ];
    }

    /**
     * Use custom empty state view with Commatix design system
     */
    public function getEmptyStateView(): ?string
    {
        return 'filament.pages.tasks.empty-state';
    }
}
