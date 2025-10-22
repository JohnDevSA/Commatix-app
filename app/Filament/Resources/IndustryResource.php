<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndustryResource\Pages;
use App\Filament\Traits\HasGlassmorphicForms;
use App\Models\Industry;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class IndustryResource extends Resource
{
    use HasGlassmorphicForms;

    protected static ?string $model = Industry::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Industries';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Industry Information')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                FormComponents\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Financial Services'),

                                FormComponents\TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('e.g., financial_services')
                                    ->helperText('Unique identifier (use underscores, lowercase)'),
                            ]),

                        FormComponents\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Description of this industry sector...')
                            ->columnSpanFull(),

                        Components\Grid::make(3)
                            ->schema([
                                FormComponents\TextInput::make('icon')
                                    ->placeholder('🏦')
                                    ->helperText('Emoji or icon for display'),

                                FormComponents\ColorPicker::make('color')
                                    ->default('#6366f1')
                                    ->helperText('Color for badges and UI elements'),

                                FormComponents\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Display order (lower numbers first)'),
                            ]),
                    ]),

                Components\Section::make('South African Compliance')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                FormComponents\Toggle::make('requires_fica')
                                    ->label('Requires FICA Compliance')
                                    ->helperText('Financial Intelligence Centre Act requirements'),

                                FormComponents\Toggle::make('requires_bee_compliance')
                                    ->label('Requires B-BBEE Compliance')
                                    ->default(true)
                                    ->helperText('Broad-Based Black Economic Empowerment'),
                            ]),

                        FormComponents\TagsInput::make('sic_codes')
                            ->label('SIC Codes')
                            ->placeholder('Add Standard Industrial Classification codes')
                            ->helperText('South African SIC codes for this industry'),

                        FormComponents\TagsInput::make('typical_compliance_requirements')
                            ->label('Compliance Requirements')
                            ->placeholder('POPIA, SARS, etc.')
                            ->helperText('Typical regulatory compliance requirements'),

                        FormComponents\TagsInput::make('regulatory_bodies')
                            ->label('Regulatory Bodies')
                            ->placeholder('FSB, SARS, Department of Health, etc.')
                            ->helperText('Relevant regulatory authorities'),
                    ]),

                Components\Section::make('Industry Configuration')
                    ->schema([
                        FormComponents\TextInput::make('typical_workflow_duration_days')
                            ->label('Typical Workflow Duration')
                            ->numeric()
                            ->default(30)
                            ->suffix('days')
                            ->helperText('Average workflow completion time for this industry'),

                        FormComponents\TagsInput::make('common_document_types')
                            ->label('Common Document Types')
                            ->placeholder('FICA Documents, Tax Certificates, etc.')
                            ->helperText('Typical documents used in this industry'),
                    ]),

                Components\Section::make('Status')
                    ->schema([
                        FormComponents\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Whether this industry is available for selection'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->width(80)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Industry')
                    ->searchable(['name'])
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->display_name),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('typical_workflow_duration_days')
                    ->label('Avg Duration')
                    ->suffix(' days')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('requires_fica')
                    ->label('FICA')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('requires_bee_compliance')
                    ->label('B-BBEE')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Only'),
                Tables\Filters\TernaryFilter::make('requires_fica')
                    ->label('FICA Required'),
                Tables\Filters\TernaryFilter::make('requires_bee_compliance')
                    ->label('B-BBEE Required'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIndustries::route('/'),
            'create' => Pages\CreateIndustry::route('/create'),
            'view' => Pages\ViewIndustry::route('/{record}'),
            'edit' => Pages\EditIndustry::route('/{record}/edit'),
        ];
    }
}
