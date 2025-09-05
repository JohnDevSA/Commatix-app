<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('trading_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('unique_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('company_registration_number')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('vat_number')
                            ->maxLength(15),
                        Forms\Components\TextInput::make('tax_reference_number')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('primary_contact_person')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('primary_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('primary_phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('billing_contact_person')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('billing_email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('billing_phone')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\Fieldset::make('Physical Address')
                            ->schema([
                                Forms\Components\TextInput::make('physical_address_line1')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('physical_address_line2')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('physical_city')
                                    ->maxLength(100),
                                Forms\Components\Select::make('physical_province')
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
                                    ]),
                                Forms\Components\TextInput::make('physical_postal_code')
                                    ->maxLength(10),
                            ])
                            ->columns(2),
                        Forms\Components\Fieldset::make('Postal Address')
                            ->schema([
                                Forms\Components\TextInput::make('postal_address_line1')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('postal_address_line2')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('postal_city')
                                    ->maxLength(100),
                                Forms\Components\Select::make('postal_province')
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
                                    ]),
                                Forms\Components\TextInput::make('postal_code')
                                    ->maxLength(10),
                            ])
                            ->columns(2),
                    ]),

                Forms\Components\Section::make('Subscription & Billing')
                    ->schema([
                        Forms\Components\Select::make('subscription_tier')
                            ->options([
                                'trial' => 'Trial',
                                'basic' => 'Basic',
                                'professional' => 'Professional',
                                'enterprise' => 'Enterprise',
                            ])
                            ->required(),
                        Forms\Components\Select::make('billing_cycle')
                            ->options([
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'annually' => 'Annually',
                            ]),
                        Forms\Components\DatePicker::make('subscription_start_date'),
                        Forms\Components\DatePicker::make('subscription_end_date'),
                        Forms\Components\TextInput::make('monthly_spend_limit')
                            ->numeric()
                            ->prefix('R'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'ZAR' => 'South African Rand (ZAR)',
                                'USD' => 'US Dollar (USD)',
                            ])
                            ->default('ZAR'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Verification')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                                'trial' => 'Trial',
                            ])
                            ->required()
                            ->default('trial'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified'),
                        Forms\Components\Toggle::make('onboarding_completed')
                            ->label('Onboarding Completed'),
                        Forms\Components\TextInput::make('onboarding_step')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trading_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('primary_email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_tier')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
