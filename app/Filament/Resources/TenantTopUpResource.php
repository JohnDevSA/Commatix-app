<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantTopUpResource\Pages;
use App\Filament\Traits\HasGlassmorphicForms;
use App\Models\TenantTopUp;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TenantTopUpResource extends Resource
{
    use HasGlassmorphicForms;

    protected static ?string $model = TenantTopUp::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|UnitEnum|null $navigationGroup = 'Tenant Management';

    protected static ?string $navigationLabel = 'Credit Top-Ups';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Credit Top-Up Information')
                    ->description('Add communication credits to a tenant account')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                FormComponents\Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Select a tenant...')
                                    ->helperText('Choose the tenant to receive credits')
                                    ->columnSpan(2),

                                FormComponents\Select::make('channel')
                                    ->label('Channel Type')
                                    ->options([
                                        'sms' => 'SMS Credits',
                                        'email' => 'Email Credits',
                                        'whatsapp' => 'WhatsApp Credits',
                                        'voice' => 'Voice Call Credits',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Select channel type...')
                                    ->helperText('Communication channel to top up')
                                    ->columnSpan(1),

                                FormComponents\TextInput::make('amount')
                                    ->label('Credit Amount')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->suffix('credits')
                                    ->placeholder('1000')
                                    ->helperText('Number of credits to add')
                                    ->columnSpan(1),

                                FormComponents\Textarea::make('reason')
                                    ->label('Top-Up Reason')
                                    ->required()
                                    ->rows(3)
                                    ->placeholder('e.g., Monthly top-up allocation, Marketing campaign credits, Customer support request')
                                    ->helperText('Document the reason for this top-up (audit trail)')
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->extraAttributes(self::glassCard('slide-up', 0.1))
                    ->collapsible()
                    ->persistCollapsed()
                    ->compact(),

                Components\Section::make('Important Notes')
                    ->description('Please review before submitting')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        FormComponents\Placeholder::make('notes')
                            ->content('
                                • Credits will be added immediately to the tenant\'s account
                                • An audit log entry will be created automatically
                                • The tenant will be notified of the credit addition
                                • This action cannot be undone - use credit adjustments to reverse
                            ')
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes(self::glassCard('slide-up', 0.2))
                    ->collapsible()
                    ->collapsed(),
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
                        FormComponents\DatePicker::make('from'),
                        FormComponents\DatePicker::make('until'),
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
                Actions\ViewAction::make(),
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
