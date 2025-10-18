<?php

namespace App\Filament\Resources\WorkflowTemplateResource\Pages;

use App\Filament\Resources\WorkflowTemplateResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use App\Models\Milestone;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowTemplate extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = WorkflowTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function afterCreate(): void
    {
        // Copy milestones if creating from parent template
        if ($this->record->parent_template_id && $this->data['copy_milestones'] ?? false) {
            $this->copyMilestonesFromParent();
        }

        // Lock workflow for milestone editing
        $this->record->lockWorkflow(auth()->user(), 'Setting up milestones');

        Notification::make()
            ->title('Workflow Created')
            ->body('Your workflow has been created. Now add milestones to complete setup.')
            ->success()
            ->send();
    }

    protected function copyMilestonesFromParent(): void
    {
        $parentTemplate = $this->record->parentTemplate;

        if (! $parentTemplate) {
            return;
        }

        foreach ($parentTemplate->milestones as $milestone) {
            Milestone::create([
                'workflow_template_id' => $this->record->id,
                'name' => $milestone->name,
                'description' => $milestone->description,
                'estimated_duration_days' => $milestone->estimated_duration_days,
                'sequence_order' => $milestone->sequence_order,
                'requires_approval' => $milestone->requires_approval,
                'requires_docs' => $milestone->requires_docs,
                'actions' => $milestone->actions,
                'milestone_type' => $milestone->milestone_type,
                'status_type_id' => $milestone->status_type_id,
            ]);
        }

        // Update total estimated duration
        $this->record->update([
            'estimated_duration_days' => $parentTemplate->estimated_duration_days,
        ]);
    }
}
