<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowTemplateResource\Pages;
use App\Filament\Resources\WorkflowTemplateResource\RelationManagers;
use App\Models\WorkflowTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class WorkflowTemplateResource extends Resource
{
    protected static ?string $model = WorkflowTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Super Admin';

    protected static ?string $navigationLabel = 'Global Workflows';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('template_type')
                                    ->options([
                                        'system' => 'System Template',
                                        'industry' => 'Industry Template',
                                        'custom' => 'Custom Template',
                                        'copied' => 'Copied from Template',
                                    ])
                                    ->default('custom')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                    $state === 'copied' ? $set('parent_template_id', null) : null
                                    ),
                                Forms\Components\Select::make('industry_category')
                                    ->options([
                                        'financial_services' => 'Financial Services (FICA/KYC)',
                                        'healthcare' => 'Healthcare (Appointments)',
                                        'education' => 'Education (Enrollment)',
                                        'real_estate' => 'Real Estate (Lead Nurturing)',
                                        'retail' => 'Retail & E-commerce',
                                        'manufacturing' => 'Manufacturing',
                                        'construction' => 'Construction',
                                        'hospitality' => 'Hospitality & Tourism',
                                        'logistics' => 'Logistics & Transportation',
                                        'government' => 'Government Services',
                                        'other' => 'Other Industry',
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->helperText('Select the industry this workflow is designed for'),
                                Forms\Components\TextInput::make('template_version')
                                    ->default('1.0')
                                    ->required(),
                            ])
                            ->columnSpan(2),

                        Forms\Components\Section::make('Template Source')
                            ->schema([
                                Forms\Components\Select::make('parent_template_id')
                                    ->relationship('parentTemplate', 'name')
                                    ->searchable()
                                    ->visible(fn (callable $get) => $get('template_type') === 'copied')
                                    ->helperText('Select existing template to copy from')
                                    ->options(function () {
                                        return WorkflowTemplate::where('is_published', true)
                                            ->where('template_type', '!=', 'custom')
                                            ->pluck('name', 'id');
                                    }),
                                Forms\Components\Toggle::make('copy_milestones')
                                    ->label('Copy Milestones from Parent')
                                    ->default(true)
                                    ->visible(fn (callable $get) => $get('template_type') === 'copied'),
                                Forms\Components\TextArea::make('customization_notes')
                                    ->label('Customization Notes')
                                    ->visible(fn (callable $get) => $get('template_type') === 'copied')
                                    ->helperText('Describe how this differs from the parent template'),
                            ])
                            ->columnSpan(1)
                            ->visible(fn (callable $get) => in_array($get('template_type'), ['copied', 'industry'])),

                        Forms\Components\Section::make('Categorization')
                            ->schema([
                                Forms\Components\TextInput::make('industry_category')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('category')
                                    ->maxLength(255),
                                Forms\Components\Select::make('complexity_level')
                                    ->options([
                                        'simple' => 'Simple',
                                        'medium' => 'Medium',
                                        'complex' => 'Complex'
                                    ])
                                    ->default('medium')
                                    ->required(),
                                Forms\Components\TextInput::make('estimated_duration_days')
                                    ->numeric()
                                    ->suffix('days'),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Section::make('Configuration')
                            ->schema([
                                Forms\Components\Select::make('access_scope_id')
                                    ->relationship('accessScope', 'name')
                                    ->required(),
                                Forms\Components\Select::make('parent_template_id')
                                    ->relationship('parentTemplate', 'name')
                                    ->searchable(),
                                Forms\Components\TagsInput::make('tags')
                                    ->separator(','),
                            ])
                            ->columnSpan(1),
                    ]),

                Forms\Components\Section::make('Description & Content')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->rows(4),
                                Forms\Components\Textarea::make('change_log')
                                    ->rows(4)
                                    ->placeholder('Document template changes...'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('channels')
                                    ->label('Communication Channels (JSON)')
                                    ->rows(6)
                                    ->helperText('Define available communication channels')
                                    ->placeholder('{"email": true, "sms": false, "whatsapp": true}'),
                                Forms\Components\Textarea::make('steps')
                                    ->label('Workflow Steps (JSON)')
                                    ->rows(6)
                                    ->helperText('Define the workflow step sequence')
                                    ->placeholder('[{"step": 1, "name": "Initial Review"}, ...]'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Section::make('Permissions & Visibility')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_public')
                                            ->label('Public Template')
                                            ->helperText('Visible to all tenants'),
                                        Forms\Components\Toggle::make('is_system_template')
                                            ->label('System Template')
                                            ->helperText('Core system template'),
                                        Forms\Components\Toggle::make('is_customizable')
                                            ->label('Allow Customization')
                                            ->default(true),
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Published')
                                            ->helperText('Available for use'),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ]),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publication Date'),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Section::make('Advanced Settings')
                            ->schema([
                                Forms\Components\Textarea::make('locked_milestones')
                                    ->label('Locked Milestones (JSON)')
                                    ->rows(3)
                                    ->helperText('Milestone IDs that cannot be modified')
                                    ->placeholder('[1, 3, 5]'),
                                Forms\Components\Textarea::make('required_roles')
                                    ->label('Required Roles (JSON)')
                                    ->rows(3)
                                    ->helperText('Roles required to use this template')
                                    ->placeholder('["admin", "manager"]'),
                            ])
                            ->columnSpan(1)
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'system' => 'danger',
                        'global' => 'success',
                        'industry' => 'info',
                        'custom' => 'warning',
                        'copied' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('industry_category')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('complexity_level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'simple' => 'success',
                        'medium' => 'warning',
                        'complex' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('estimated_duration_days')
                    ->numeric()
                    ->suffix(' days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('template_type')
                    ->options([
                        'system' => 'System',
                        'industry' => 'Industry',
                        'custom' => 'Custom',
                        'copied' => 'Copied',
                        'global' => 'Global'
                    ]),
                Tables\Filters\SelectFilter::make('complexity_level')
                    ->options([
                        'simple' => 'Simple',
                        'medium' => 'Medium',
                        'complex' => 'Complex'
                    ]),
                Tables\Filters\TernaryFilter::make('is_published'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray')
                    ->action(function (WorkflowTemplate $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name . ' (Copy)';
                        $newRecord->is_published = false;
                        $newRecord->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->icon('heroicon-m-eye')
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => true])),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->icon('heroicon-m-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => false])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MilestonesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflowTemplates::route('/'),
            'create' => Pages\CreateWorkflowTemplate::route('/create'),
            'view' => Pages\ViewWorkflowTemplate::route('/{record}'),
            'edit' => Pages\EditWorkflowTemplate::route('/{record}/edit'),
        ];
    }
}
