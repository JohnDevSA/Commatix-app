<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = DocumentTypeResource::class;
}
