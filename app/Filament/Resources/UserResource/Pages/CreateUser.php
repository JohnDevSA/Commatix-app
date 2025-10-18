<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = UserResource::class;
}
