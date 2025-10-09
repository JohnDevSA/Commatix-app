<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionResource\Pages;
use App\Models\Division;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Divisions';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return (auth()->user()?->isTenantAdmin() || auth()->user()?->isSuperAdmin()) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Tenant admins only see their divisions
        if (auth()->user()?->isTenantAdmin()) {
            return $query->where('tenant_id', auth()->user()->tenant_id);
        }

        // Super admins see all divisions
        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Division Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Name of the division or department')
                            ->placeholder('e.g., Sales, Marketing, Operations'),

                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required()
                            ->visible(fn () => auth()->user()?->isSuperAdmin())
                            ->searchable()
                            ->helperText('Select the tenant this division belongs to'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office-2')
                    ->description(fn (Division $record) =>
                        "{$record->users()->count()} users, {$record->workflowTemplates()->count()} workflows"
                    ),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->isSuperAdmin())
                    ->toggleable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('workflow_templates_count')
                    ->label('Workflows')
                    ->counts('workflowTemplates')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s'))
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'view' => Pages\ViewDivision::route('/{record}'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }
}
