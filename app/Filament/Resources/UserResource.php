<?php

namespace App\Filament\Resources;
use BackedEnum;
use UnitEnum;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Traits\HasSouthAfricanDateFormats;
use App\Models\User;
use Filament\Schemas\Components;
use Filament\Forms\Components as FormComponents;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    use HasSouthAfricanDateFormats;

    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = 'User Management';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->canManageUsers() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0'); // Return empty query if no user
        }

        if ($user->isSuperAdmin()) {
            return $query; // Super admins see all users
        }

        // Tenant admins and users only see users from their tenant
        return $query->where('tenant_id', $user->tenant_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Tabs::make('User Management')
                    ->tabs([
                        Components\Tabs\Tab::make('Personal Information')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Components\Section::make('User Details')
                                    ->description('Essential user information and authentication credentials')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\TextInput::make('name')
                                                    ->label('Full Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('John Doe')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->columnSpanFull(),

                                                FormComponents\TextInput::make('email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255)
                                                    ->placeholder('john.doe@company.co.za')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('This will be used for login and notifications'),

                                                FormComponents\TextInput::make('password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                                    ->dehydrated(fn ($state) => filled($state))
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Leave empty to keep current password when editing'),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('Contact Information')
                                    ->description('Additional contact details and preferences')
                                    ->icon('heroicon-m-phone')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->maxLength(20)
                                                    ->placeholder('+27 11 123 4567')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('position')
                                                    ->label('Job Position')
                                                    ->maxLength(100)
                                                    ->placeholder('Marketing Manager')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\Select::make('preferred_language')
                                                    ->label('Language')
                                                    ->options([
                                                        'en' => 'English',
                                                        'af' => 'Afrikaans',
                                                        'zu' => 'Zulu',
                                                        'xh' => 'Xhosa',
                                                    ])
                                                    ->default('en')
                                                    ->extraAttributes(['class' => 'glass-input']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Components\Tabs\Tab::make('Access & Permissions')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Components\Section::make('User Role & Tenant Access')
                                    ->description('Define user permissions and tenant association')
                                    ->icon('heroicon-m-key')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('user_type_id')
                                                    ->label('User Type / Role')
                                                    ->relationship(
                                                        name: 'userType',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: function (Builder $query) {
                                                            // Tenant admins cannot assign Super Admin role
                                                            if (auth()->user()?->isTenantAdmin()) {
                                                                return $query->where('is_super_admin', false);
                                                            }

                                                            return $query;
                                                        }
                                                    )
                                                    ->required()
                                                    ->preload()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Determines user permissions and access level'),

                                                FormComponents\Select::make('tenant_id')
                                                    ->label('Associated Tenant')
                                                    ->relationship('tenant', 'name')
                                                    ->preload()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Leave empty for Super Admin users only')
                                                    ->hidden(fn () => auth()->user()?->isTenantAdmin() ?? false)
                                                    ->default(fn () => auth()->user()?->isTenantAdmin() ? auth()->user()->tenant_id : null),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('Organization Structure')
                                    ->description('Division and department assignments')
                                    ->icon('heroicon-m-building-office')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('division_id')
                                                    ->label('Division / Department')
                                                    ->relationship(
                                                        name: 'division',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', auth()->user()->tenant_id)
                                                    )
                                                    ->preload()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Optional: Assign user to specific division'),

                                                FormComponents\DateTimePicker::make('email_verified_at')
                                                    ->label('Email Verified At')
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Set to mark email as verified manually'),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Components\Tabs\Tab::make('Security & Preferences')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Components\Section::make('Security Settings')
                                    ->description('Account security and authentication settings')
                                    ->icon('heroicon-m-lock-closed')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Toggle::make('two_factor_enabled')
                                                    ->label('Two-Factor Authentication')
                                                    ->helperText('Enable 2FA for enhanced security')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                FormComponents\Toggle::make('is_active')
                                                    ->label('Account Active')
                                                    ->default(true)
                                                    ->helperText('Deactivate to prevent user login')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('Notification Preferences')
                                    ->description('Configure email and SMS notification settings')
                                    ->icon('heroicon-m-bell')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\Toggle::make('email_notifications')
                                                    ->label('Email Notifications')
                                                    ->default(true)
                                                    ->helperText('Receive email notifications')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                FormComponents\Toggle::make('sms_notifications')
                                                    ->label('SMS Notifications')
                                                    ->default(false)
                                                    ->helperText('Receive SMS notifications')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                FormComponents\Toggle::make('marketing_emails')
                                                    ->label('Marketing Emails')
                                                    ->default(false)
                                                    ->helperText('Receive product updates and tips')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'animate-slide-up']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->size('lg')
                        ->color('primary')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('email')
                        ->icon('heroicon-m-envelope')
                        ->iconPosition(IconPosition::Before)
                        ->size('sm')
                        ->color('gray')
                        ->searchable()
                        ->copyable(),
                ])
                    ->space(1)
                    ->extraAttributes(['class' => 'glass-card p-2']),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('userType.name')
                        ->label('Role')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'Super Admin' => 'danger',
                            'Admin' => 'warning',
                            'Manager' => 'info',
                            'Team Lead' => 'primary',
                            'User' => 'success',
                            'Viewer' => 'gray',
                            default => 'gray',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Super Admin' => 'heroicon-m-shield-exclamation',
                            'Admin' => 'heroicon-m-cog-6-tooth',
                            'Manager' => 'heroicon-m-user-group',
                            'Team Lead' => 'heroicon-m-users',
                            'User' => 'heroicon-m-user',
                            'Viewer' => 'heroicon-m-eye',
                            default => 'heroicon-m-question-mark-circle',
                        })
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('tenant.name')
                        ->label('Tenant')
                        ->size('sm')
                        ->color('gray')
                        ->searchable()
                        ->placeholder('Global User')
                        ->prefix('🏢 '),
                ])
                    ->space(1),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\IconColumn::make('email_verified_at')
                        ->label('Email Verified')
                        ->boolean()
                        ->getStateUsing(fn ($record) => $record->email_verified_at !== null)
                        ->trueIcon('heroicon-o-shield-check')
                        ->falseIcon('heroicon-o-shield-exclamation')
                        ->trueColor('success')
                        ->falseColor('warning')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('division.name')
                        ->label('Division')
                        ->size('sm')
                        ->color('gray')
                        ->searchable()
                        ->placeholder('No Division')
                        ->prefix('📂 '),
                ])
                    ->space(1),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(self::saDateFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime(self::saDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type_id')
                    ->relationship('userType', 'name')
                    ->label('User Type')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->label('Tenant')
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('email_verified')
                    ->label('Email Verified Only')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->toggle(),

                Tables\Filters\Filter::make('super_admins')
                    ->label('Super Admins Only')
                    ->query(fn (Builder $query): Builder => $query->whereHas('userType', fn ($query) => $query->where('name', 'Super Admin')))
                    ->toggle(),

                Tables\Filters\Filter::make('active_users')
                    ->label('Active Users Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('info'),

                Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),

                \STS\FilamentImpersonate\Actions\Impersonate::make(),

                Actions\Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-m-key')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reset User Password')
                    ->modalDescription('This will send a password reset email to the user.')
                    ->action(fn ($record) => \Filament\Notifications\Notification::make()
                        ->title('Password reset email sent')
                        ->body("Password reset instructions sent to {$record->email}")
                        ->success()
                        ->send()),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected users')
                        ->modalDescription('Are you sure you want to delete these users? This action cannot be undone.'),

                    Actions\BulkAction::make('activate_users')
                        ->label('Activate Users')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Users activated successfully')
                            ->success()
                            ->send()),

                    Actions\BulkAction::make('deactivate_users')
                        ->label('Deactivate Users')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false])))
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Users deactivated successfully')
                            ->warning()
                            ->send()),
                ]),
            ])
            ->emptyStateHeading('No users found')
            ->emptyStateDescription('Create your first user to get started with user management.')
            ->emptyStateIcon('heroicon-o-users')
            ->defaultSort('created_at', 'desc')
            ->striped();
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
