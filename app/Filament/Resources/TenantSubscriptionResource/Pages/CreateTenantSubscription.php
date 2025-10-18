<?php

namespace App\Filament\Resources\TenantSubscriptionResource\Pages;

use App\Filament\Resources\TenantSubscriptionResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantSubscription extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TenantSubscriptionResource::class;
}
