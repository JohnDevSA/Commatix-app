<?php

namespace App\Filament\Resources\TenantTopUpResource\Pages;

use App\Filament\Resources\TenantTopUpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantTopUp extends EditRecord
{
    protected static string $resource = TenantTopUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
