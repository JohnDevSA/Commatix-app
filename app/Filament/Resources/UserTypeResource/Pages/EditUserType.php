<?php

namespace App\Filament\Resources\UserTypeResource\Pages;

use App\Filament\Resources\UserTypeResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserType extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = UserTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
