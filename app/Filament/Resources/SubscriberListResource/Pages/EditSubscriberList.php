<?php

namespace App\Filament\Resources\SubscriberListResource\Pages;

use App\Filament\Resources\SubscriberListResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriberList extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = SubscriberListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
