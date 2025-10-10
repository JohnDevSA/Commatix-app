<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantTopUpResource\Pages;
use App\Models\TenantTopUp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantTopUpResource extends Resource
{
    protected static ?string $model = TenantTopUp::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Tenant Management';

    protected static ?string $navigationLabel = 'Credit Top-Ups';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Top-Up Details')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->label('Tenant')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select the tenant to add credits to'),

                        Forms\Components\Select::make('channel')
                            ->options([
                                'sms' => 'SMS',
                                'email' => 'Email',
                                'whatsapp' => 'WhatsApp',
                                'voice' => 'Voice Calls',
                            ])
                            ->required()
                            ->helperText('Communication channel to top up'),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->suffix('credits')
                            ->helperText('Number of credits to add'),

                        Forms\Components\Textarea::make('reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('e.g., Monthly top-up, Marketing campaign, Customer request')
                            ->helperText('Reason for this top-up (for audit trail)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->tenant?->name),

                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sms' => 'success',
                        'email' => 'info',
                        'whatsapp' => 'warning',
                        'voice' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->suffix(' credits')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->reason)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('addedBy.name')
                    ->label('Added By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'sms' => 'SMS',
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                        'voice' => 'Voice',
                    ]),

                Tables\Filters\SelectFilter::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Tenant'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantTopUps::route('/'),
            'create' => Pages\CreateTenantTopUp::route('/create'),
        ];
    }
}
