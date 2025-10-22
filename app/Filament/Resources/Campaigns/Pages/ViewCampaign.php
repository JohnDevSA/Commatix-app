<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Contracts\Services\CampaignServiceInterface;
use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => in_array($record->status, ['draft', 'scheduled'])),

            Action::make('send')
                ->label('Send Now')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Send Campaign Now?')
                ->modalDescription(fn ($record) => "This will immediately send the campaign '{$record->name}' to {$record->total_recipients} recipients.")
                ->modalSubmitActionLabel('Send Campaign')
                ->action(function ($record) {
                    $campaignService = app(CampaignServiceInterface::class);

                    try {
                        $campaignService->sendCampaign($record);

                        Notification::make()
                            ->title('Campaign Started')
                            ->body("Campaign '{$record->name}' is now being sent.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Campaign Send Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => $record->canBeSent()),

            Action::make('schedule')
                ->label('Schedule')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Schedule Campaign')
                ->modalDescription(fn ($record) => "Mark campaign '{$record->name}' as scheduled.")
                ->action(function ($record) {
                    $campaignService = app(CampaignServiceInterface::class);

                    try {
                        $campaignService->scheduleCampaign($record, $record->scheduled_at);

                        Notification::make()
                            ->title('Campaign Scheduled')
                            ->body("Campaign '{$record->name}' has been scheduled.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Schedule Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => $record->isDraft() && $record->scheduled_at),

            Action::make('pause')
                ->label('Pause')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $campaignService = app(CampaignServiceInterface::class);

                    try {
                        $campaignService->pauseCampaign($record);

                        Notification::make()
                            ->title('Campaign Paused')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Pause Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => $record->isSending()),

            Action::make('resume')
                ->label('Resume')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $campaignService = app(CampaignServiceInterface::class);

                    try {
                        $campaignService->resumeCampaign($record);

                        Notification::make()
                            ->title('Campaign Resumed')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Resume Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => $record->isPaused()),

            Action::make('cancel')
                ->label('Cancel Campaign')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel Campaign?')
                ->modalDescription('This will stop all pending messages. This action cannot be undone.')
                ->action(function ($record) {
                    $campaignService = app(CampaignServiceInterface::class);

                    try {
                        $campaignService->cancelCampaign($record);

                        Notification::make()
                            ->title('Campaign Cancelled')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Cancel Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => in_array($record->status, ['scheduled', 'sending', 'paused'])),
        ];
    }
}
