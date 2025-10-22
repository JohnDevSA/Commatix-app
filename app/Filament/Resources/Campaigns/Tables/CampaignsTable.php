<?php

namespace App\Filament\Resources\Campaigns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description ? \Illuminate\Support\Str::limit($record->description, 50) : null),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sending' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'paused' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('messageTemplate.name')
                    ->label('Template')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('messageTemplate.channel')
                    ->label('Channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'email' => 'info',
                        'sms' => 'success',
                        'whatsapp' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('subscriberList.name')
                    ->label('List')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('total_recipients')
                    ->label('Recipients')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('sent_count')
                    ->label('Sent')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->color('success')
                    ->description(fn ($record) => $record->total_recipients > 0
                        ? round(($record->sent_count / $record->total_recipients) * 100, 1).'%'
                        : null),

                TextColumn::make('delivered_count')
                    ->label('Delivered')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('failed_count')
                    ->label('Failed')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->color('danger')
                    ->toggleable()
                    ->visible(fn ($record) => $record && $record->failed_count > 0),

                TextColumn::make('opened_count')
                    ->label('Opens')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->toggleable()
                    ->description(fn ($record) => $record->delivered_count > 0
                        ? round(($record->opened_count / $record->delivered_count) * 100, 1).'%'
                        : null),

                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'paused' => 'Paused',
                    ])
                    ->multiple(),

                SelectFilter::make('channel')
                    ->relationship('messageTemplate', 'channel')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
