<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivision extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
