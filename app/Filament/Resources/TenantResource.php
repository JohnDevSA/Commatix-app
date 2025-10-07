<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tenant Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Company Details')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                Forms\Components\Section::make('Company Information')
                                    ->description('Essential business registration and legal information for South African compliance')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Registered Company Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Enter full registered company name')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->columnSpanFull(),

                                                Forms\Components\TextInput::make('trading_name')
                                                    ->label('Trading Name (if different)')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter trading name')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('unique_code')
                                                    ->label('Unique Tenant Code')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(50)
                                                    ->placeholder('COMP001')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('This code will be used for tenant identification'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('SA Business Registration')
                                    ->description('South African business compliance and registration details')
                                    ->icon('heroicon-m-document-check')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('company_registration_number')
                                                    ->label('CK Number')
                                                    ->maxLength(20)
                                                    ->placeholder('2019/123456/07')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Company registration number'),

                                                Forms\Components\TextInput::make('vat_number')
                                                    ->label('VAT Number')
                                                    ->maxLength(15)
                                                    ->placeholder('4123456789')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('10-digit VAT number'),

                                                Forms\Components\TextInput::make('tax_reference_number')
                                                    ->label('Tax Reference Number')
                                                    ->maxLength(20)
                                                    ->placeholder('9876543210')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('SARS tax reference'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contact & Address')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Forms\Components\Section::make('Primary Contact Information')
                                    ->description('Main business contact details')
                                    ->icon('heroicon-m-user')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('primary_contact_person')
                                                    ->label('Contact Person')
                                                    ->maxLength(255)
                                                    ->placeholder('John Doe')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('primary_email')
                                                    ->label('Primary Email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('contact@company.co.za')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('primary_phone')
                                                    ->label('Primary Phone')
                                                    ->tel()
                                                    ->maxLength(20)
                                                    ->placeholder('+27 11 123 4567')
                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Billing Contact Information')
                                    ->description('Billing and financial contact details')
                                    ->icon('heroicon-m-credit-card')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('billing_contact_person')
                                                    ->label('Billing Contact')
                                                    ->maxLength(255)
                                                    ->placeholder('Jane Smith')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('billing_email')
                                                    ->label('Billing Email')
                                                    ->email()
                                                    ->maxLength(255)
                                                    ->placeholder('billing@company.co.za')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('billing_phone')
                                                    ->label('Billing Phone')
                                                    ->tel()
                                                    ->maxLength(20)
                                                    ->placeholder('+27 11 765 4321')
                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),

                                Forms\Components\Section::make('Address Information')
                                    ->description('Physical and postal address details')
                                    ->icon('heroicon-m-map')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Physical Address')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('physical_address_line1')
                                                            ->label('Address Line 1')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        Forms\Components\TextInput::make('physical_address_line2')
                                                            ->label('Address Line 2')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('physical_city')
                                                                    ->label('City')
                                                                    ->maxLength(100)
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                                Forms\Components\Select::make('physical_province')
                                                                    ->label('Province')
                                                                    ->options([
                                                                        'EC' => 'Eastern Cape',
                                                                        'FS' => 'Free State',
                                                                        'GP' => 'Gauteng',
                                                                        'KZN' => 'KwaZulu-Natal',
                                                                        'LP' => 'Limpopo',
                                                                        'MP' => 'Mpumalanga',
                                                                        'NC' => 'Northern Cape',
                                                                        'NW' => 'North West',
                                                                        'WC' => 'Western Cape',
                                                                    ])
                                                                    ->searchable()
                                                                    ->extraAttributes(['class' => 'glass-input']),
                                                                Forms\Components\TextInput::make('physical_postal_code')
                                                                    ->label('Postal Code')
                                                                    ->maxLength(10)
                                                                    ->placeholder('1234')
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                            ])
                                                    ])
                                                    ->extraAttributes(['class' => 'border-l-4 border-commatix-500 pl-4']),

                                                Forms\Components\Fieldset::make('Postal Address')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('postal_address_line1')
                                                            ->label('Address Line 1')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        Forms\Components\TextInput::make('postal_address_line2')
                                                            ->label('Address Line 2')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('postal_city')
                                                                    ->label('City')
                                                                    ->maxLength(100)
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                                Forms\Components\Select::make('postal_province')
                                                                    ->label('Province')
                                                                    ->options([
                                                                        'EC' => 'Eastern Cape',
                                                                        'FS' => 'Free State',
                                                                        'GP' => 'Gauteng',
                                                                        'KZN' => 'KwaZulu-Natal',
                                                                        'LP' => 'Limpopo',
                                                                        'MP' => 'Mpumalanga',
                                                                        'NC' => 'Northern Cape',
                                                                        'NW' => 'North West',
                                                                        'WC' => 'Western Cape',
                                                                    ])
                                                                    ->searchable()
                                                                    ->extraAttributes(['class' => 'glass-input']),
                                                                Forms\Components\TextInput::make('postal_code')
                                                                    ->label('Postal Code')
                                                                    ->maxLength(10)
                                                                    ->placeholder('1234')
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                            ])
                                                    ])
                                                    ->extraAttributes(['class' => 'border-l-4 border-sa-gold-500 pl-4']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.2s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Subscription & Billing')
                            ->icon('heroicon-m-credit-card')
                            ->schema([
                                Forms\Components\Section::make('Subscription Details')
                                    ->description('Service tier and billing configuration')
                                    ->icon('heroicon-m-star')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('subscription_tier')
                                                    ->label('Service Tier')
                                                    ->options([
                                                        'trial' => 'Trial (14 days)',
                                                        'basic' => 'Basic - R 299/month',
                                                        'professional' => 'Professional - R 599/month',
                                                        'enterprise' => 'Enterprise - R 1,299/month',
                                                    ])
                                                    ->required()
                                                    ->searchable()
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                Forms\Components\Select::make('billing_cycle')
                                                    ->label('Billing Cycle')
                                                    ->options([
                                                        'monthly' => 'Monthly',
                                                        'quarterly' => 'Quarterly (5% discount)',
                                                        'annually' => 'Annually (10% discount)',
                                                    ])
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                Forms\Components\Select::make('currency')
                                                    ->label('Currency')
                                                    ->options([
                                                        'ZAR' => 'South African Rand (ZAR)',
                                                        'USD' => 'US Dollar (USD)',
                                                    ])
                                                    ->default('ZAR')
                                                    ->extraAttributes(['class' => 'glass-input']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Billing Dates & Limits')
                                    ->description('Subscription periods and spending controls')
                                    ->icon('heroicon-m-calendar-days')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\DatePicker::make('subscription_start_date')
                                                    ->label('Subscription Start')
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                Forms\Components\DatePicker::make('subscription_end_date')
                                                    ->label('Subscription End')
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('monthly_spend_limit')
                                                    ->label('Monthly Spend Limit')
                                                    ->numeric()
                                                    ->prefix('R')
                                                    ->placeholder('5000')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Optional spending cap for usage-based billing'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Status & Verification')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Forms\Components\Section::make('Account Status')
                                    ->description('Current account status and verification details')
                                    ->icon('heroicon-m-check-circle')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('status')
                                                    ->label('Account Status')
                                                    ->options([
                                                        'active' => 'Active',
                                                        'inactive' => 'Inactive',
                                                        'suspended' => 'Suspended',
                                                        'trial' => 'Trial',
                                                    ])
                                                    ->required()
                                                    ->default('trial')
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('onboarding_step')
                                                    ->label('Onboarding Progress')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(10)
                                                    ->placeholder('5')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Current step in onboarding process (1-10)'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Verification & Compliance')
                                    ->description('Account verification and compliance status')
                                    ->icon('heroicon-m-document-check')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('is_verified')
                                                    ->label('Account Verified')
                                                    ->helperText('Email and identity verification completed')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('onboarding_completed')
                                                    ->label('Onboarding Completed')
                                                    ->helperText('Full setup and configuration completed')
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

                    Tables\Columns\TextColumn::make('trading_name')
                        ->color('gray')
                        ->size('sm')
                        ->searchable()
                        ->placeholder('No trading name'),
                ])
                    ->space(1)
                    ->extraAttributes(['class' => 'glass-card p-2']),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('unique_code')
                        ->label('Tenant Code')
                        ->badge()
                        ->color('secondary')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('primary_email')
                        ->icon('heroicon-m-envelope')
                        ->iconPosition(IconPosition::Before)
                        ->size('sm')
                        ->color('gray')
                        ->searchable()
                        ->copyable(),
                ])
                    ->space(1),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'info',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-m-check-circle',
                        'trial' => 'heroicon-m-clock',
                        'inactive' => 'heroicon-m-pause-circle',
                        'suspended' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'trial' => 'Trial',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                        default => 'Unknown',
                    })
                    ->sortable(),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\IconColumn::make('is_verified')
                        ->label('Verified')
                        ->boolean()
                        ->trueIcon('heroicon-o-shield-check')
                        ->falseIcon('heroicon-o-shield-exclamation')
                        ->trueColor('success')
                        ->falseColor('warning')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('subscription_tier')
                        ->label('Tier')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'enterprise' => 'primary',
                            'professional' => 'success',
                            'basic' => 'info',
                            'trial' => 'warning',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->sortable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'trial' => 'Trial',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->label('Subscription Tier')
                    ->options([
                        'trial' => 'Trial',
                        'basic' => 'Basic',
                        'professional' => 'Professional',
                        'enterprise' => 'Enterprise',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('verified_only')
                    ->label('Verified Tenants')
                    ->query(fn (Builder $query): Builder => $query->where('is_verified', true))
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

                // Switch tenant action removed - dashboard route not configured
                // Tables\Actions\Action::make('switch_tenant')
                //     ->label('Switch')
                //     ->icon('heroicon-m-arrow-right-circle')
                //     ->color('primary')
                //     ->url(fn (Tenant $record): string => route('filament.app.pages.dashboard', ['tenant' => $record->id]))
                //     ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('No tenants found')
            ->emptyStateDescription('Create your first tenant to get started with multi-tenant management.')
            ->emptyStateIcon('heroicon-o-building-office-2')
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
