<?php

namespace App\Filament\Resources\TenantUsageResource\Pages;

use App\Filament\Resources\TenantUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantUsages extends ListRecords
{
    protected static string $resource = TenantUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
