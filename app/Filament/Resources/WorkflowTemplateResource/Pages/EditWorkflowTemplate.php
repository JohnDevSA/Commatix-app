<?php

namespace App\Filament\Resources\WorkflowTemplateResource\Pages;

use App\Filament\Resources\WorkflowTemplateResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowTemplate extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = WorkflowTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('publish')
                ->label('Publish Workflow')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => ! $this->record->is_published)
                ->requiresConfirmation()
                ->modalHeading('Publish Workflow Template')
                ->modalDescription('Are you sure you want to publish this workflow? It will become available to users.')
                ->action(function () {
                    if ($this->validateForPublishing()) {
                        $this->record->update([
                            'is_published' => true,
                            'published_at' => now(),
                        ]);

                        $this->record->unlockWorkflow();

                        Notification::make()
                            ->title('Workflow Published')
                            ->body('Your workflow is now available to users.')
                            ->success()
                            ->send();
                    }
                }),

            Actions\Action::make('unpublish')
                ->label('Unpublish')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->visible(fn () => $this->record->is_published)
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'is_published' => false,
                        'published_at' => null,
                    ]);

                    Notification::make()
                        ->title('Workflow Unpublished')
                        ->body('This workflow is no longer available to users.')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate Workflow')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->action(function () {
                    $newWorkflow = $this->record->replicate();
                    $newWorkflow->name = $this->record->name.' (Copy)';
                    $newWorkflow->is_published = false;
                    $newWorkflow->published_at = null;
                    $newWorkflow->parent_template_id = $this->record->id;
                    $newWorkflow->template_type = 'copied';
                    $newWorkflow->save();

                    // Copy milestones
                    foreach ($this->record->milestones as $milestone) {
                        $newMilestone = $milestone->replicate();
                        $newMilestone->workflow_template_id = $newWorkflow->id;
                        $newMilestone->save();
                    }

                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $newWorkflow]));
                }),

            Actions\DeleteAction::make()
                ->visible(fn () => ! $this->record->is_published),
        ];
    }

    protected function validateForPublishing(): bool
    {
        // Check if workflow has milestones
        if ($this->record->milestones()->count() === 0) {
            Notification::make()
                ->title('Cannot Publish')
                ->body('Workflow must have at least one milestone before publishing.')
                ->danger()
                ->send();

            return false;
        }

        // Check if all milestones have required fields
        $incompleteMilestones = $this->record->milestones()
            ->whereNull('estimated_duration_days')
            ->orWhereNull('description')
            ->orWhereNull('milestone_type')
            ->count();

        if ($incompleteMilestones > 0) {
            Notification::make()
                ->title('Cannot Publish')
                ->body('All milestones must have duration, description, and type before publishing.')
                ->danger()
                ->send();

            return false;
        }

        return true;
    }

    protected function beforeSave(): void
    {
        // Calculate total estimated duration
        $totalDuration = $this->record->milestones()->sum('estimated_duration_days');
        $this->record->estimated_duration_days = $totalDuration;
    }
}
