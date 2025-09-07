<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';


    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('User Management')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Information')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Forms\Components\Section::make('User Details')
                                    ->description('Essential user information and authentication credentials')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Full Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('John Doe')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->columnSpanFull(),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255)
                                                    ->placeholder('john.doe@company.co.za')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('This will be used for login and notifications'),

                                                Forms\Components\TextInput::make('password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                                    ->dehydrated(fn ($state) => filled($state))
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Leave empty to keep current password when editing'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Contact Information')
                                    ->description('Additional contact details and preferences')
                                    ->icon('heroicon-m-phone')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->maxLength(20)
                                                    ->placeholder('+27 11 123 4567')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('position')
                                                    ->label('Job Position')
                                                    ->maxLength(100)
                                                    ->placeholder('Marketing Manager')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\Select::make('preferred_language')
                                                    ->label('Language')
                                                    ->options([
                                                        'en' => 'English',
                                                        'af' => 'Afrikaans',
                                                        'zu' => 'Zulu',
                                                        'xh' => 'Xhosa',
                                                    ])
                                                    ->default('en')
                                                    ->extraAttributes(['class' => 'glass-input']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Access & Permissions')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Forms\Components\Section::make('User Role & Tenant Access')
                                    ->description('Define user permissions and tenant association')
                                    ->icon('heroicon-m-key')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('user_type_id')
                                                    ->label('User Type / Role')
                                                    ->relationship('userType', 'name')
                                                    ->required()
                                                    ->preload()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Determines user permissions and access level'),

                                                Forms\Components\Select::make('tenant_id')
                                                    ->label('Associated Tenant')
                                                    ->relationship('tenant', 'name')
                                                    ->preload()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Leave empty for Super Admin users only'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Organization Structure')
                                    ->description('Division and department assignments')
                                    ->icon('heroicon-m-building-office')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('division_id')
                                                    ->label('Division / Department')
                                                    ->relationship('division', 'name')
                                                    ->preload()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Optional: Assign user to specific division'),

                                                Forms\Components\DateTimePicker::make('email_verified_at')
                                                    ->label('Email Verified At')
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Set to mark email as verified manually'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Security & Preferences')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Security Settings')
                                    ->description('Account security and authentication settings')
                                    ->icon('heroicon-m-lock-closed')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('two_factor_enabled')
                                                    ->label('Two-Factor Authentication')
                                                    ->helperText('Enable 2FA for enhanced security')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Account Active')
                                                    ->default(true)
                                                    ->helperText('Deactivate to prevent user login')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Notification Preferences')
                                    ->description('Configure email and SMS notification settings')
                                    ->icon('heroicon-m-bell')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Toggle::make('email_notifications')
                                                    ->label('Email Notifications')
                                                    ->default(true)
                                                    ->helperText('Receive email notifications')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('sms_notifications')
                                                    ->label('SMS Notifications')
                                                    ->default(false)
                                                    ->helperText('Receive SMS notifications')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('marketing_emails')
                                                    ->label('Marketing Emails')
                                                    ->default(false)
                                                    ->helperText('Receive product updates and tips')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ])
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
                            'Tenant Admin' => 'warning',
                            'Tenant Manager' => 'info',
                            'Tenant User' => 'success',
                            'Tenant Viewer' => 'gray',
                            default => 'gray',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Super Admin' => 'heroicon-m-shield-exclamation',
                            'Tenant Admin' => 'heroicon-m-cog-6-tooth',
                            'Tenant Manager' => 'heroicon-m-user-group',
                            'Tenant User' => 'heroicon-m-user',
                            'Tenant Viewer' => 'heroicon-m-eye',
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
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
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
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),

                Tables\Actions\Action::make('impersonate')
                    ->label('Impersonate')
                    ->icon('heroicon-m-user-circle')
                    ->color('primary')
                    ->visible(fn ($record) => auth()->user()->isSuperAdmin() && !$record->isSuperAdmin())
                    ->requiresConfirmation()
                    ->modalHeading('Impersonate User')
                    ->modalDescription('Are you sure you want to impersonate this user? You will be logged in as them.')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->action(fn ($record) => \Filament\Notifications\Notification::make()
                        ->title('Impersonation feature coming soon')
                        ->body('This feature will be available in the next update.')
                        ->info()
                        ->send()),

                Tables\Actions\Action::make('reset_password')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected users')
                        ->modalDescription('Are you sure you want to delete these users? This action cannot be undone.'),

                    Tables\Actions\BulkAction::make('activate_users')
                        ->label('Activate Users')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Users activated successfully')
                            ->success()
                            ->send()),

                    Tables\Actions\BulkAction::make('deactivate_users')
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
