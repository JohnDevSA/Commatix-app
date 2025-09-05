<?php

namespace App\Filament\Resources\TenantSubscriptionResource\Pages;

use App\Filament\Resources\TenantSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantSubscriptions extends ListRecords
{
    protected static string $resource = TenantSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
