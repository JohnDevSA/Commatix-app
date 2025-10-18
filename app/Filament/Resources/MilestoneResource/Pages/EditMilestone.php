<?php

namespace App\Filament\Resources\MilestoneResource\Pages;

use App\Filament\Resources\MilestoneResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMilestone extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = MilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
