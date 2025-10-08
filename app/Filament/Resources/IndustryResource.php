<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndustryResource\Pages;
use App\Models\Industry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IndustryResource extends Resource
{
    protected static ?string $model = Industry::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Industries';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Industry Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Financial Services'),

                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('e.g., financial_services')
                                    ->helperText('Unique identifier (use underscores, lowercase)'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Description of this industry sector...')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->placeholder('🏦')
                                    ->helperText('Emoji or icon for display'),

                                Forms\Components\ColorPicker::make('color')
                                    ->default('#6366f1')
                                    ->helperText('Color for badges and UI elements'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Display order (lower numbers first)'),
                            ]),
                    ]),

                Forms\Components\Section::make('South African Compliance')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('requires_fica')
                                    ->label('Requires FICA Compliance')
                                    ->helperText('Financial Intelligence Centre Act requirements'),

                                Forms\Components\Toggle::make('requires_bee_compliance')
                                    ->label('Requires B-BBEE Compliance')
                                    ->default(true)
                                    ->helperText('Broad-Based Black Economic Empowerment'),
                            ]),

                        Forms\Components\TagsInput::make('sic_codes')
                            ->label('SIC Codes')
                            ->placeholder('Add Standard Industrial Classification codes')
                            ->helperText('South African SIC codes for this industry'),

                        Forms\Components\TagsInput::make('typical_compliance_requirements')
                            ->label('Compliance Requirements')
                            ->placeholder('POPIA, SARS, etc.')
                            ->helperText('Typical regulatory compliance requirements'),

                        Forms\Components\TagsInput::make('regulatory_bodies')
                            ->label('Regulatory Bodies')
                            ->placeholder('FSB, SARS, Department of Health, etc.')
                            ->helperText('Relevant regulatory authorities'),
                    ]),

                Forms\Components\Section::make('Industry Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('typical_workflow_duration_days')
                            ->label('Typical Workflow Duration')
                            ->numeric()
                            ->default(30)
                            ->suffix('days')
                            ->helperText('Average workflow completion time for this industry'),

                        Forms\Components\TagsInput::make('common_document_types')
                            ->label('Common Document Types')
                            ->placeholder('FICA Documents, Tax Certificates, etc.')
                            ->helperText('Typical documents used in this industry'),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
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
