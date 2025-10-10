<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalGroupResource\Pages;
use App\Models\ApprovalGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApprovalGroupResource extends Resource
{
    protected static ?string $model = ApprovalGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Approval Groups';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return (auth()->user()?->isTenantAdmin() || auth()->user()?->isSuperAdmin()) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isTenantAdmin()) {
            return $query->where('tenant_id', auth()->user()->tenant_id);
        }

        return $query; // Super admins see all
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Approval Group Information')
                    ->description('Create groups of users who can approve tasks and workflows')
                    ->icon('heroicon-m-user-group')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Group Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Finance Approvers, Management Team')
                                    ->extraInputAttributes(['class' => 'glass-input'])
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('division_id')
                                    ->label('Division')
                                    ->relationship(
                                        name: 'division',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', auth()->user()->tenant_id)
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->extraAttributes(['class' => 'glass-input'])
                                    ->helperText('Optional: Restrict this group to a specific division'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive groups cannot be used for approvals')
                                    ->extraAttributes(['class' => 'glass-card']),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->maxLength(65535)
                                    ->placeholder('Describe the purpose and responsibilities of this approval group')
                                    ->extraInputAttributes(['class' => 'glass-input'])
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])
                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                Forms\Components\Section::make('Group Members')
                    ->description('Select users who belong to this approval group')
                    ->icon('heroicon-m-users')
                    ->schema([
                        Forms\Components\Select::make('users')
                            ->label('Members')
                            ->multiple()
                            ->relationship(
                                name: 'users',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, ?ApprovalGroup $record) {
                                    $query->where('tenant_id', auth()->user()->tenant_id);

                                    // If division is set, filter users by division
                                    if ($record && $record->division_id) {
                                        $query->where('division_id', $record->division_id);
                                    }

                                    return $query;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->extraAttributes(['class' => 'glass-input'])
                            ->helperText('Users must be from the same division if one is selected')
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),

                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->visible(fn () => auth()->user()?->isSuperAdmin())
                    ->searchable()
                    ->preload()
                    ->extraAttributes(['class' => 'glass-input']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-group')
                    ->weight('bold')
                    ->description(fn (ApprovalGroup $record) => $record->description),

                Tables\Columns\TextColumn::make('division.name')
                    ->label('Division')
                    ->searchable()
                    ->sortable()
                    ->placeholder('All Divisions')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Members')
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->isSuperAdmin())
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s'))
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('division_id')
                    ->relationship('division', 'name')
                    ->label('Division')
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('active_only')
                    ->label('Active Groups Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Groups')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Groups activated successfully')
                            ->success()
                            ->send()),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Groups')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false])))
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Groups deactivated successfully')
                            ->warning()
                            ->send()),
                ]),
            ])
            ->emptyStateHeading('No approval groups found')
            ->emptyStateDescription('Create your first approval group to manage workflow approvals.')
            ->emptyStateIcon('heroicon-o-user-group');
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
            'index' => Pages\ListApprovalGroups::route('/'),
            'create' => Pages\CreateApprovalGroup::route('/create'),
            'view' => Pages\ViewApprovalGroup::route('/{record}'),
            'edit' => Pages\EditApprovalGroup::route('/{record}/edit'),
        ];
    }
}
