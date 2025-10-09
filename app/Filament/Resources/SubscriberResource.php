<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use App\Models\SubscriberList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = 'Subscribers';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return (auth()->user()?->isTenantAdmin() || auth()->user()?->isSuperAdmin()) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tenant_id', auth()->user()->tenant_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('subscriber_list_id')
                            ->label('Subscriber List')
                            ->options(function () {
                                return SubscriberList::where('tenant_id', auth()->user()->tenant_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Assign subscriber to a list'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'unsubscribed' => 'Unsubscribed',
                                'bounced' => 'Bounced',
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\DateTimePicker::make('opt_in_date')
                            ->label('Opt-in Date')
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('opt_out_date')
                            ->label('Opt-out Date'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->helperText('Add tags to categorize this subscriber'),

                        Forms\Components\KeyValue::make('custom_fields')
                            ->helperText('Add custom fields for additional data'),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subscriberList.name')
                    ->label('List')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->subscriberList?->name),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'unsubscribed' => 'warning',
                        'bounced' => 'danger',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('opt_in_date')
                    ->label('Opt-in')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tags')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s'))
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'unsubscribed' => 'Unsubscribed',
                        'bounced' => 'Bounced',
                    ]),

                Tables\Filters\SelectFilter::make('subscriber_list_id')
                    ->label('Subscriber List')
                    ->options(function () {
                        return SubscriberList::where('tenant_id', auth()->user()->tenant_id)
                            ->pluck('name', 'id');
                    })
                    ->searchable(),

                Tables\Filters\Filter::make('opt_in_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('opt_in_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('opt_in_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('changeStatus')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'unsubscribed' => 'Unsubscribed',
                                    'bounced' => 'Bounced',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['status' => $data['status']]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('moveToList')
                        ->label('Move to List')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->form([
                            Forms\Components\Select::make('subscriber_list_id')
                                ->label('Target List')
                                ->options(function () {
                                    return SubscriberList::where('tenant_id', auth()->user()->tenant_id)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['subscriber_list_id' => $data['subscriber_list_id']]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}
