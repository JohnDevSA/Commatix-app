<?php

namespace App\Filament\Resources\ApprovalGroupResource\Pages;

use App\Filament\Resources\ApprovalGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApprovalGroup extends EditRecord
{
    protected static string $resource = ApprovalGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
