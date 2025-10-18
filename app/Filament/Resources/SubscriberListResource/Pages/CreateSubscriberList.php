<?php

namespace App\Filament\Resources\SubscriberListResource\Pages;

use App\Filament\Resources\SubscriberListResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriberList extends CreateRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = SubscriberListResource::class;
}
