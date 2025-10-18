<?php

namespace App\Filament\Resources\TenantUsageResource\Pages;

use App\Filament\Resources\TenantUsageResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantUsage extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TenantUsageResource::class;
}
