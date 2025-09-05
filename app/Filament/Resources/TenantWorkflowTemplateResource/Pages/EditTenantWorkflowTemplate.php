<?php

namespace App\Filament\Resources\TenantWorkflowTemplateResource\Pages;

use App\Filament\Resources\TenantWorkflowTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTenantWorkflowTemplate extends EditRecord
{
    protected static string $resource = TenantWorkflowTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('publish')
                ->label('Publish Workflow')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => !$this->record->is_published && $this->canPublish())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'is_published' => true,
                        'published_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Workflow Published')
                        ->body('Your workflow is now active and ready to use.')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->visible(fn () => !$this->record->is_published),
        ];
    }

    protected function canPublish(): bool
    {
        return $this->record->milestones()->count() > 0;
    }
}
