<?php

namespace App\Filament\Resources\UserTypeResource\Pages;

use App\Filament\Resources\UserTypeResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserType extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = UserTypeResource::class;
}
