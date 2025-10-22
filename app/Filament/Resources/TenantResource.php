<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Traits\HasSouthAfricanDateFormats;
use App\Models\Tenant;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TenantResource extends Resource
{
    use HasSouthAfricanDateFormats;

    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'Tenant Management';

    protected static ?string $navigationLabel = 'Tenants';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Tabs::make('Tenant Information')
                    ->tabs([
                        Components\Tabs\Tab::make('Company Details')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                Components\Section::make('Company Information')
                                    ->description('Essential business registration and legal information for South African compliance')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\TextInput::make('name')
                                                    ->label('Registered Company Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Enter full registered company name')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->columnSpanFull(),

                                                FormComponents\TextInput::make('trading_name')
                                                    ->label('Trading Name (if different)')
                                                    ->maxLength(255)
                                                    ->placeholder('Enter trading name')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('unique_code')
                                                    ->label('Unique Tenant Code')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(50)
                                                    ->placeholder('COMP001')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('This code will be used for tenant identification'),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('SA Business Registration')
                                    ->description('South African business compliance and registration details')
                                    ->icon('heroicon-m-document-check')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\TextInput::make('company_registration_number')
                                                    ->label('CK Number')
                                                    ->maxLength(20)
                                                    ->placeholder('2019/123456/07')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Company registration number'),

                                                FormComponents\TextInput::make('vat_number')
                                                    ->label('VAT Number')
                                                    ->maxLength(15)
                                                    ->placeholder('4123456789')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('10-digit VAT number'),

                                                FormComponents\TextInput::make('tax_reference_number')
                                                    ->label('Tax Reference Number')
                                                    ->maxLength(20)
                                                    ->placeholder('9876543210')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('SARS tax reference'),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Components\Tabs\Tab::make('Contact & Address')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Components\Section::make('Primary Contact Information')
                                    ->description('Main business contact details')
                                    ->icon('heroicon-m-user')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\TextInput::make('primary_contact_person')
                                                    ->label('Contact Person')
                                                    ->maxLength(255)
                                                    ->placeholder('John Doe')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('primary_email')
                                                    ->label('Primary Email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('contact@company.co.za')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('primary_phone')
                                                    ->label('Primary Phone')
                                                    ->tel()
                                                    ->maxLength(20)
                                                    ->placeholder('+27 11 123 4567')
                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('Billing Contact Information')
                                    ->description('Billing and financial contact details')
                                    ->icon('heroicon-m-credit-card')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\TextInput::make('billing_contact_person')
                                                    ->label('Billing Contact')
                                                    ->maxLength(255)
                                                    ->placeholder('Jane Smith')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('billing_email')
                                                    ->label('Billing Email')
                                                    ->email()
                                                    ->maxLength(255)
                                                    ->placeholder('billing@company.co.za')
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('billing_phone')
                                                    ->label('Billing Phone')
                                                    ->tel()
                                                    ->maxLength(20)
                                                    ->placeholder('+27 11 765 4321')
                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),

                                Components\Section::make('Address Information')
                                    ->description('Physical and postal address details')
                                    ->icon('heroicon-m-map')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\Fieldset::make('Physical Address')
                                                    ->schema([
                                                        FormComponents\TextInput::make('physical_address_line1')
                                                            ->label('Address Line 1')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        FormComponents\TextInput::make('physical_address_line2')
                                                            ->label('Address Line 2')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        Components\Grid::make(3)
                                                            ->schema([
                                                                FormComponents\TextInput::make('physical_city')
                                                                    ->label('City')
                                                                    ->maxLength(100)
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                                FormComponents\Select::make('physical_province')
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
                                                                FormComponents\TextInput::make('physical_postal_code')
                                                                    ->label('Postal Code')
                                                                    ->maxLength(10)
                                                                    ->placeholder('1234')
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                            ]),
                                                    ])
                                                    ->extraAttributes(['class' => 'border-l-4 border-commatix-500 pl-4']),

                                                Components\Fieldset::make('Postal Address')
                                                    ->schema([
                                                        FormComponents\TextInput::make('postal_address_line1')
                                                            ->label('Address Line 1')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        FormComponents\TextInput::make('postal_address_line2')
                                                            ->label('Address Line 2')
                                                            ->maxLength(255)
                                                            ->extraInputAttributes(['class' => 'glass-input']),
                                                        Components\Grid::make(3)
                                                            ->schema([
                                                                FormComponents\TextInput::make('postal_city')
                                                                    ->label('City')
                                                                    ->maxLength(100)
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                                FormComponents\Select::make('postal_province')
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
                                                                FormComponents\TextInput::make('postal_code')
                                                                    ->label('Postal Code')
                                                                    ->maxLength(10)
                                                                    ->placeholder('1234')
                                                                    ->extraInputAttributes(['class' => 'glass-input']),
                                                            ]),
                                                    ])
                                                    ->extraAttributes(['class' => 'border-l-4 border-sa-gold-500 pl-4']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.2s']),
                            ]),

                        Components\Tabs\Tab::make('Subscription & Billing')
                            ->icon('heroicon-m-credit-card')
                            ->schema([
                                Components\Section::make('Subscription Details')
                                    ->description('Service tier and billing configuration')
                                    ->icon('heroicon-m-star')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\Select::make('subscription_tier')
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

                                                FormComponents\Select::make('billing_cycle')
                                                    ->label('Billing Cycle')
                                                    ->options([
                                                        'monthly' => 'Monthly',
                                                        'quarterly' => 'Quarterly (5% discount)',
                                                        'annually' => 'Annually (10% discount)',
                                                    ])
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                FormComponents\Select::make('currency')
                                                    ->label('Currency')
                                                    ->options([
                                                        'ZAR' => 'South African Rand (ZAR)',
                                                        'USD' => 'US Dollar (USD)',
                                                    ])
                                                    ->default('ZAR')
                                                    ->extraAttributes(['class' => 'glass-input']),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('Billing Dates & Limits')
                                    ->description('Subscription periods and spending controls')
                                    ->icon('heroicon-m-calendar-days')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                FormComponents\DatePicker::make('subscription_start_date')
                                                    ->label('Subscription Start')
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                FormComponents\DatePicker::make('subscription_end_date')
                                                    ->label('Subscription End')
                                                    ->extraAttributes(['class' => 'glass-input']),

                                                FormComponents\TextInput::make('monthly_spend_limit')
                                                    ->label('Monthly Spend Limit')
                                                    ->numeric()
                                                    ->prefix('R')
                                                    ->placeholder('5000')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Optional spending cap for usage-based billing'),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Components\Tabs\Tab::make('Status & Verification')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Components\Section::make('Account Status')
                                    ->description('Current account status and verification details')
                                    ->icon('heroicon-m-check-circle')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('status')
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

                                                FormComponents\TextInput::make('onboarding_step')
                                                    ->label('Onboarding Progress')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(10)
                                                    ->placeholder('5')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Current step in onboarding process (1-10)'),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Components\Section::make('Verification & Compliance')
                                    ->description('Account verification and compliance status')
                                    ->icon('heroicon-m-document-check')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Toggle::make('is_verified')
                                                    ->label('Account Verified')
                                                    ->helperText('Email and identity verification completed')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                FormComponents\Toggle::make('onboarding_completed')
                                                    ->label('Onboarding Completed')
                                                    ->helperText('Full setup and configuration completed')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Company Name')
                    ->description(fn ($record) => $record->trading_name ?: 'No trading name')
                    ->searchable(['name', 'trading_name'])
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('primary')
                    ->wrap(),

                Tables\Columns\TextColumn::make('unique_code')
                    ->label('Code')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Code copied!')
                    ->tooltip('Click to copy'),

                Tables\Columns\TextColumn::make('primary_email')
                    ->label('Contact Email')
                    ->icon('heroicon-m-envelope')
                    ->iconPosition(IconPosition::Before)
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->tooltip('Click to copy')
                    ->limit(30),

                Tables\Columns\TextColumn::make('primary_phone')
                    ->label('Phone')
                    ->icon('heroicon-m-phone')
                    ->iconPosition(IconPosition::Before)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('No phone'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable()
                    ->tooltip(fn ($record) => $record->is_verified ? 'Account verified' : 'Verification pending'),

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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(self::saDateFormat())
                    ->sortable()
                    ->color('gray')
                    ->since()
                    ->tooltip(fn ($record) => 'Created: '.$record->created_at->format(self::saDateTimeFormat())),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime(self::saDateFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'trial' => 'Trial',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->multiple()
                    ->indicator('Status'),

                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->label('Subscription Tier')
                    ->options([
                        'trial' => 'Trial',
                        'basic' => 'Basic',
                        'professional' => 'Professional',
                        'enterprise' => 'Enterprise',
                    ])
                    ->multiple()
                    ->indicator('Tier'),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->placeholder('All tenants')
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only')
                    ->indicator('Verification'),

                Tables\Filters\Filter::make('created_this_month')
                    ->label('Created This Month')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->startOfMonth())
                    )
                    ->toggle()
                    ->indicator('This Month'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->persistFiltersInSession()
            ->filtersTriggerAction(
                fn (Actions\Action $action) => $action
                    ->button()
                    ->label('Filters')
                    ->icon('heroicon-m-funnel')
            )
            ->actions([
                Actions\ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->color('info'),

                Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (\Illuminate\Support\Collection $records) => $records->each->update(['status' => 'active'])
                        ),

                    Actions\BulkAction::make('suspend')
                        ->label('Suspend Selected')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Suspend Tenants')
                        ->modalDescription('Are you sure you want to suspend these tenants? They will lose access to the platform.')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (\Illuminate\Support\Collection $records) => $records->each->update(['status' => 'suspended'])
                        ),

                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Tenants')
                        ->modalDescription('Are you sure? This will permanently delete the tenant and all associated data.'),
                ]),
            ])
            ->emptyStateHeading('No tenants found')
            ->emptyStateDescription('Create your first tenant to get started with multi-tenant management.')
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateActions([
                Actions\Action::make('create')
                    ->label('Create First Tenant')
                    ->url(fn (): string => static::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button()
                    ->color('primary'),
            ])
            ->poll('30s')
            ->deferLoading()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->persistColumnSearchesInSession();
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
