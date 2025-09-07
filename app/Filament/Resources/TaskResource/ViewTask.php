<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('start_early')
                ->label('Start Early')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->visible(fn () => $this->record->canStartEarly())
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for early start')
                        ->required()
                        ->placeholder('Please explain why this task needs to start early...')
                ])
                ->action(function (array $data) {
                    $this->record->startTask($data['reason']);

                    \Filament\Notifications\Notification::make()
                        ->title('Task Started Early')
                        ->body('Task has been started before its scheduled date.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('move_to_next')
                ->label('Complete Current Step')
                ->icon('heroicon-o-arrow-right')
                ->color('success')
                ->visible(fn () => $this->record->status === 'in_progress' && $this->record->current_milestone_id)
                ->requiresConfirmation()
                ->action(function () {
                    $result = $this->record->moveToNextMilestone();

                    if ($result['success']) {
                        \Filament\Notifications\Notification::make()
                            ->title($result['message'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title($result['message'])
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }
}
