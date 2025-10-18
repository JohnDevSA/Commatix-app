<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function beforeCreate(): void
    {
        $user = auth()->user();

        // Set default values
        $this->data['created_by'] = $user->id;

        // Set tenant_id if in tenant context
        if (tenant()) {
            $this->data['tenant_id'] = tenant()->id;
        } elseif ($user->tenant_id) {
            // Fallback to user's tenant if not in tenant context
            $this->data['tenant_id'] = $user->tenant_id;
        }

        // Set division_id from the assigned user or current user's division
        if (empty($this->data['division_id'])) {
            // If a specific user is assigned, use their division
            if (! empty($this->data['assigned_to'])) {
                $assignedUser = \App\Models\User::find($this->data['assigned_to']);
                if ($assignedUser && $assignedUser->division_id) {
                    $this->data['division_id'] = $assignedUser->division_id;
                }
            } else {
                // Otherwise use current user's division
                if ($user->division_id) {
                    $this->data['division_id'] = $user->division_id;
                }
            }
        }

        // Auto-assign to current user if not specified
        if (empty($this->data['assigned_to'])) {
            $this->data['assigned_to'] = $user->id;
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
