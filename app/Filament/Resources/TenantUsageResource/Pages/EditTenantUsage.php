<?php

namespace App\Filament\Resources\TenantUsageResource\Pages;

use App\Filament\Resources\TenantUsageResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantUsage extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TenantUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
