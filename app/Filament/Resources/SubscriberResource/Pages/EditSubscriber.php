<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriber extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
