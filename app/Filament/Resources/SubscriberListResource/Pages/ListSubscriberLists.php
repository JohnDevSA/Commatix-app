<?php

namespace App\Filament\Resources\SubscriberListResource\Pages;

use App\Filament\Resources\SubscriberListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubscriberLists extends ListRecords
{
    protected static string $resource = SubscriberListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
