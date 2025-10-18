<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateMilestone extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = MilestoneResource::class;
}
