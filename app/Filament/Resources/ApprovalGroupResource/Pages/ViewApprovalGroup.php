<?php

namespace App\Filament\Resources\ApprovalGroupResource\Pages;

use App\Filament\Resources\ApprovalGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApprovalGroup extends ViewRecord
{
    protected static string $resource = ApprovalGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}