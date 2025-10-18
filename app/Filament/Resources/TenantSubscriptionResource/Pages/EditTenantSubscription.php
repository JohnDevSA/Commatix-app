<?php

namespace App\Filament\Resources\TenantSubscriptionResource\Pages;

use App\Filament\Resources\TenantSubscriptionResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantSubscription extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TenantSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
