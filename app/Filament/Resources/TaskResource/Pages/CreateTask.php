<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function beforeCreate(): void
    {
        // Set default values
        $this->data['created_by'] = auth()->id();

        // Set tenant_id if in tenant context
        if (tenant()) {
            $this->data['tenant_id'] = tenant()->id;
        }

        // Auto-assign to current user if not specified
        if (empty($this->data['assigned_to'])) {
            $this->data['assigned_to'] = auth()->id();
        }
    }

    protected function afterCreate(): void
    {
        $task = $this->record;

        // Auto-start if scheduled for today or past
        if ($task->shouldAutoStart()) {
            $task->startTask();
        }

        Notification::make()
            ->title('Task Created')
            ->body("Task '{$task->title}' has been created successfully.")
            ->success()
            ->send();
    }
}
