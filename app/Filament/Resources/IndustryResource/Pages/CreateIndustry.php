<?php

namespace App\Filament\Resources\IndustryResource\Pages;

use App\Filament\Resources\IndustryResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateIndustry extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = IndustryResource::class;
}
