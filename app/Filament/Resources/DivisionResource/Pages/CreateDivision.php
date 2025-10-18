<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = DivisionResource::class;
}
