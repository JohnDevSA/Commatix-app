<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TenantResource::class;
}
