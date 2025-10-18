<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriber extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = SubscriberResource::class;
}
