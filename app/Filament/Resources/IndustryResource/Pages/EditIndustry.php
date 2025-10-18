<?php

namespace App\Filament\Resources\IndustryResource\Pages;

use App\Filament\Resources\IndustryResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIndustry extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = IndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
