<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantUsageResource\Pages;
use App\Models\TenantUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantUsageResource extends Resource
{
    protected static ?string $model = TenantUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Tenant Management';

    protected static ?string $navigationLabel = 'Usage Monitoring';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('period_start'),
                Forms\Components\DateTimePicker::make('period_end'),
                Forms\Components\TextInput::make('emails_sent')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('sms_sent')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('whatsapp_sent')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('voice_calls')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('storage_used_mb')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('api_calls')
                    ->required()
                    ->numeric()
                    ->default(0),
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
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->tenant?->name),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Period Start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_end')
                    ->label('Period End')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('emails_sent')
                    ->label('Emails')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()),
                Tables\Columns\TextColumn::make('sms_sent')
                    ->label('SMS')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('whatsapp_sent')
                    ->label('WhatsApp')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('voice_calls')
                    ->label('Voice Calls')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('storage_used_mb')
                    ->label('Storage (MB)')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('api_calls')
                    ->label('API Calls')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->label('Tenant')
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('period')
                    ->form([
                        Forms\Components\DatePicker::make('period_from')
                            ->label('Period From'),
                        Forms\Components\DatePicker::make('period_to')
                            ->label('Period To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['period_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('period_start', '>=', $date),
                            )
                            ->when(
                                $data['period_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('period_end', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('period_start', 'desc');
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
            'index' => Pages\ListTenantUsages::route('/'),
            'create' => Pages\CreateTenantUsage::route('/create'),
            'edit' => Pages\EditTenantUsage::route('/{record}/edit'),
        ];
    }
}
