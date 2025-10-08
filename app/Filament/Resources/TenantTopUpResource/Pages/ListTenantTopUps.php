<?php

namespace App\Filament\Resources\TenantTopUpResource\Pages;

use App\Filament\Resources\TenantTopUpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantTopUps extends ListRecords
{
    protected static string $resource = TenantTopUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
