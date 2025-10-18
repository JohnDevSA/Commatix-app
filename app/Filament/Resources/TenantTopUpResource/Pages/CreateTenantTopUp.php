<?php

namespace App\Filament\Resources\TenantTopUpResource\Pages;

use App\Contracts\Services\CreditManagementInterface;
use App\Filament\Resources\TenantTopUpResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use App\Models\Tenant;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantTopUp extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TenantTopUpResource::class;

    protected ?string $heading = 'Add Tenant Credits';

    protected ?string $subheading = 'Top up communication credits for a tenant account';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Add the user who created the top-up
        $data['added_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        // Use CreditManagementService to process the top-up
        $creditService = app(CreditManagementInterface::class);
        $tenant = Tenant::find($this->record->tenant_id);

        try {
            $creditService->addCredits(
                $tenant,
                $this->record->channel,
                $this->record->amount,
                $this->record->reason
            );

            Notification::make()
                ->success()
                ->title('Credits Added Successfully')
                ->body("Added {$this->record->amount} {$this->record->channel} credits to {$tenant->name}")
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->duration(5000)
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Failed to Add Credits')
                ->body($e->getMessage())
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->persistent()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
