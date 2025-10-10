<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        // Handle status changes
        $originalStatus = $this->record->getOriginal('status');
        $newStatus = $this->data['status'];

        if ($originalStatus !== $newStatus) {
            switch ($newStatus) {
                case 'in_progress':
                    if ($originalStatus === 'scheduled' || $originalStatus === 'draft') {
                        $this->record->startTask('Manual start via edit');
                    }
                    break;
                case 'on_hold':
                    $this->data['held_at'] = now();
                    $this->data['held_by'] = auth()->id();
                    break;
                case 'completed':
                    $this->data['completed_at'] = now();
                    $this->data['completed_by'] = auth()->id();
                    break;
            }
        }
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Task Updated')
            ->body("Task '{$this->record->title}' has been updated successfully.")
            ->success()
            ->send();
    }
}
