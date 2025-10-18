<?php

namespace App\Filament\Resources;
use BackedEnum;
use UnitEnum;

use App\Filament\Resources\UserTypeResource\Pages;
use App\Models\UserType;
use Filament\Schemas\Components;
use Filament\Forms\Components as FormComponents;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Traits\HasGlassmorphicForms;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserTypeResource extends Resource
{
    use HasGlassmorphicForms;
    protected static ?string $model = UserType::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | UnitEnum | null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'User Roles';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormComponents\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                FormComponents\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Tenant Admin' => 'warning',
                        'Tenant Manager' => 'info',
                        'Tenant User' => 'success',
                        'Tenant Viewer' => 'gray',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(60)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 60) {
                            return null;
                        }

                        return $state;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users Count')
                    ->numeric()
                    ->sortable(),
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
                Tables\Filters\Filter::make('has_users')
                    ->query(fn (Builder $query): Builder => $query->has('users'))
                    ->label('Has Users'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('view_users')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn (UserType $record): string => '/admin/users?'.http_build_query(['tableFilters' => ['user_type_id' => ['values' => [$record->id]]]]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to delete these user types? This action cannot be undone and will affect all users assigned to these roles.'),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListUserTypes::route('/'),
            'create' => Pages\CreateUserType::route('/create'),
            'edit' => Pages\EditUserType::route('/{record}/edit'),
        ];
    }
}
