<?php

namespace App\Filament\Resources\TenantTopUpResource\Pages;

use App\Filament\Resources\TenantTopUpResource;
use App\Contracts\Services\CreditManagementInterface;
use App\Models\Tenant;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTenantTopUp extends CreateRecord
{
    protected static string $resource = TenantTopUpResource::class;

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
                ->body("{$this->record->amount} {$this->record->channel} credits added to {$tenant->name}")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Adding Credits')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
