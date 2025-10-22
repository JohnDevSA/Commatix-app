<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('messageTemplate.name')
                    ->label('Message template'),
                TextEntry::make('subscriberList.name')
                    ->label('Subscriber list'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('scheduled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('total_recipients')
                    ->numeric(),
                TextEntry::make('sent_count')
                    ->numeric(),
                TextEntry::make('delivered_count')
                    ->numeric(),
                TextEntry::make('failed_count')
                    ->numeric(),
                TextEntry::make('opened_count')
                    ->numeric(),
                TextEntry::make('clicked_count')
                    ->numeric(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
