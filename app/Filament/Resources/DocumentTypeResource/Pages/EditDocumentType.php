<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentType extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = DocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
