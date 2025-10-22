<?php

namespace App\Filament\Resources\MessageTemplates\Pages;

use App\Filament\Resources\MessageTemplates\MessageTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMessageTemplate extends EditRecord
{
    protected static string $resource = MessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
