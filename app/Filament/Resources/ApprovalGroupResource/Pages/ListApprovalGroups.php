<?php

namespace App\Filament\Resources\ApprovalGroupResource\Pages;

use App\Filament\Resources\ApprovalGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovalGroups extends ListRecords
{
    protected static string $resource = ApprovalGroupResource::class;

    protected string $view = 'filament.resources.approval-group-resource.pages.list-approval-groups';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
