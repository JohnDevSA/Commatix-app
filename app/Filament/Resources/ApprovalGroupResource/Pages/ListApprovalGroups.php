<?php

namespace App\Filament\Resources\ApprovalGroupResource\Pages;

use App\Filament\Resources\ApprovalGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovalGroups extends ListRecords
{
    protected static string $resource = ApprovalGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}