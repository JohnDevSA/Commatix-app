<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Traits\HasGlassmorphicForms;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Forms\Components;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class CampaignForm
{
    use HasGlassmorphicForms;
    use HasRightAlignedFormActions;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Campaign Details')
                    ->description('Define your campaign name, description, and target audience')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Summer Sale 2025')
                            ->helperText('A clear, descriptive name for your campaign')
                            ->columnSpan(2),

                        Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Describe the purpose and goals of this campaign...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                SchemaComponents\Section::make('Message Configuration')
                    ->description('Choose your message template and target subscriber list')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Components\Select::make('message_template_id')
                            ->label('Message Template')
                            ->relationship(
                                'messageTemplate',
                                'name',
                                fn ($query) => $query
                                    ->where('tenant_id', auth()->user()->tenant_id)
                                    ->where('is_active', true)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select an active message template'),

                        Components\Select::make('subscriber_list_id')
                            ->label('Subscriber List')
                            ->relationship(
                                'subscriberList',
                                'name',
                                fn ($query) => $query->where('tenant_id', auth()->user()->tenant_id)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Target audience for this campaign'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                SchemaComponents\Section::make('Scheduling')
                    ->description('Schedule when this campaign should be sent')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Components\DateTimePicker::make('scheduled_at')
                            ->label('Schedule For')
                            ->native(false)
                            ->seconds(false)
                            ->helperText('Leave blank for draft, or schedule for a future date/time')
                            ->minDate(now())
                            ->timezone('Africa/Johannesburg'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                SchemaComponents\Section::make('Campaign Statistics')
                    ->description('Campaign performance metrics (read-only)')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        SchemaComponents\Grid::make(3)
                            ->schema([
                                Components\Placeholder::make('total_recipients')
                                    ->label('Total Recipients')
                                    ->content(fn ($record) => $record ? number_format($record->total_recipients) : '0'),

                                Components\Placeholder::make('sent_count')
                                    ->label('Sent')
                                    ->content(fn ($record) => $record ? number_format($record->sent_count) : '0'),

                                Components\Placeholder::make('delivered_count')
                                    ->label('Delivered')
                                    ->content(fn ($record) => $record ? number_format($record->delivered_count) : '0'),

                                Components\Placeholder::make('failed_count')
                                    ->label('Failed')
                                    ->content(fn ($record) => $record ? number_format($record->failed_count) : '0'),

                                Components\Placeholder::make('opened_count')
                                    ->label('Opened')
                                    ->content(fn ($record) => $record ? number_format($record->opened_count) : '0'),

                                Components\Placeholder::make('clicked_count')
                                    ->label('Clicked')
                                    ->content(fn ($record) => $record ? number_format($record->clicked_count) : '0'),
                            ]),

                        SchemaComponents\Grid::make(3)
                            ->schema([
                                Components\Placeholder::make('success_rate')
                                    ->label('Success Rate')
                                    ->content(fn ($record) => $record ? $record->getSuccessRate().'%' : '0%'),

                                Components\Placeholder::make('open_rate')
                                    ->label('Open Rate')
                                    ->content(fn ($record) => $record ? $record->getOpenRate().'%' : '0%'),

                                Components\Placeholder::make('click_rate')
                                    ->label('Click Rate')
                                    ->content(fn ($record) => $record ? $record->getClickRate().'%' : '0%'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record && ! $record->isDraft()),
            ]);
    }
}
