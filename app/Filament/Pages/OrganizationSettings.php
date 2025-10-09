<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class OrganizationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Organization Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.organization-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Only tenant admins can access (not super admins)
        return $user?->isTenantAdmin() ?? false;
    }

    public function mount(): void
    {
        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);

        $this->form->fill($tenant->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Organization Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Company Details')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                Forms\Components\Section::make('Company Information')
                                    ->description('Essential business registration and legal information')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Registered Company Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('trading_name')
                                                    ->label('Trading Name (if different)')
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(['class' => 'glass-input']),
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
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('primary_email')
                                                    ->label('Primary Email')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255)
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
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('billing_email')
                                                    ->label('Billing Email')
                                                    ->email()
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(['class' => 'glass-input']),

                                                Forms\Components\TextInput::make('billing_phone')
                                                    ->label('Billing Phone')
                                                    ->tel()
                                                    ->maxLength(20)
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
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'animate-slide-up']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);

        $tenant->update($data);

        Notification::make()
            ->title('Organization settings updated')
            ->success()
            ->send();
    }
}