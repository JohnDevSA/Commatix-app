<?php

namespace App\Filament\Resources\ApprovalGroupResource\Pages;

use App\Filament\Resources\ApprovalGroupResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateApprovalGroup extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = ApprovalGroupResource::class;
}
