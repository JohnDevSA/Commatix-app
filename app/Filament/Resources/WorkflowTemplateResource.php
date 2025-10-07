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

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Workflow Template Configuration')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\Section::make('Template Identification')
                                    ->description('Essential template information and categorization')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Template Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('FICA KYC Verification Workflow')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->columnSpanFull(),

                                                Forms\Components\Select::make('template_type')
                                                    ->label('Template Type')
                                                    ->options([
                                                        'system' => '🔧 System Template',
                                                        'industry' => '🏭 Industry Template',
                                                        'custom' => '✨ Custom Template',
                                                        'copied' => '📋 Copied from Template',
                                                    ])
                                                    ->default('custom')
                                                    ->required()
                                                    ->reactive()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                                    $state === 'copied' ? $set('parent_template_id', null) : null
                                                    ),

                                                Forms\Components\TextInput::make('template_version')
                                                    ->label('Version')
                                                    ->default('1.0')
                                                    ->required()
                                                    ->placeholder('1.0')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Semantic versioning (e.g., 1.0, 1.1, 2.0)'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Industry Classification')
                                    ->description('Categorize template for South African business sectors')
                                    ->icon('heroicon-m-building-office-2')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('industry_category')
                                                    ->label('Primary Industry')
                                                    ->options([
                                                        'financial_services' => '🏦 Financial Services (FICA/KYC)',
                                                        'healthcare' => '🏥 Healthcare (Appointments)',
                                                        'education' => '🎓 Education (Enrollment)',
                                                        'real_estate' => '🏘️ Real Estate (Lead Nurturing)',
                                                        'retail' => '🛍️ Retail & E-commerce',
                                                        'manufacturing' => '🏭 Manufacturing',
                                                        'construction' => '🏗️ Construction',
                                                        'hospitality' => '🏨 Hospitality & Tourism',
                                                        'logistics' => '🚚 Logistics & Transportation',
                                                        'government' => '🏛️ Government Services',
                                                        'other' => '📄 Other Industry',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Select the primary industry this workflow serves'),

                                                Forms\Components\Select::make('complexity_level')
                                                    ->label('Complexity Level')
                                                    ->options([
                                                        'simple' => '🟢 Simple (1-3 steps)',
                                                        'medium' => '🟡 Medium (4-8 steps)',
                                                        'complex' => '🔴 Complex (9+ steps)'
                                                    ])
                                                    ->default('medium')
                                                    ->required()
                                                    ->extraAttributes(['class' => 'glass-input']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),

                                Forms\Components\Section::make('Template Description')
                                    ->description('Detailed description and purpose of this workflow')
                                    ->icon('heroicon-m-document-text')
                                    ->schema([
                                        Forms\Components\Textarea::make('description')
                                            ->label('Template Description')
                                            ->required()
                                            ->rows(4)
                                            ->placeholder('Describe the purpose, process, and expected outcomes of this workflow template...')
                                            ->extraInputAttributes(['class' => 'glass-input'])
                                            ->columnSpanFull(),
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.2s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Template Source & Inheritance')
                            ->icon('heroicon-m-document-duplicate')
                            ->schema([
                                Forms\Components\Section::make('Parent Template Configuration')
                                    ->description('Configure template inheritance and copying behavior')
                                    ->icon('heroicon-m-arrow-up-circle')
                                    ->schema([
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Select::make('parent_template_id')
                                                    ->label('Parent Template')
                                                    ->relationship('parentTemplate', 'name')
                                                    ->searchable()
                                                    ->visible(fn (callable $get) => $get('template_type') === 'copied')
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Select existing template to copy from')
                                                    ->options(function () {
                                                        return WorkflowTemplate::where('is_published', true)
                                                            ->where('template_type', '!=', 'custom')
                                                            ->pluck('name', 'id');
                                                    }),
                                            ])
                                    ])
                                    ->visible(fn (callable $get) => $get('template_type') === 'copied')
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Copy Configuration')
                                    ->description('Define what to copy from the parent template')
                                    ->icon('heroicon-m-clipboard-document-check')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('copy_milestones')
                                                    ->label('Copy Milestones from Parent')
                                                    ->default(true)
                                                    ->helperText('Include all milestones from parent template')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('copy_settings')
                                                    ->label('Copy Settings & Configuration')
                                                    ->default(true)
                                                    ->helperText('Include communication settings and channels')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ])
                                    ])
                                    ->visible(fn (callable $get) => $get('template_type') === 'copied')
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),

                                Forms\Components\Section::make('Customization Notes')
                                    ->description('Document changes and customizations made to this template')
                                    ->icon('heroicon-m-pencil-square')
                                    ->schema([
                                        Forms\Components\Textarea::make('customization_notes')
                                            ->label('Customization Notes')
                                            ->rows(4)
                                            ->placeholder('Describe how this template differs from the parent template...')
                                            ->extraInputAttributes(['class' => 'glass-input'])
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('change_log')
                                            ->label('Change Log')
                                            ->rows(4)
                                            ->placeholder('Document template changes and version history...')
                                            ->extraInputAttributes(['class' => 'glass-input'])
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (callable $get) => in_array($get('template_type'), ['copied', 'industry']))
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.2s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Workflow Configuration')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Communication Channels')
                                    ->description('Define available communication methods for this workflow')
                                    ->icon('heroicon-m-chat-bubble-left-right')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('email_enabled')
                                                    ->label('📧 Email Notifications')
                                                    ->default(true)
                                                    ->helperText('Send email notifications')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('sms_enabled')
                                                    ->label('📱 SMS Notifications')
                                                    ->default(false)
                                                    ->helperText('Send SMS notifications')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('whatsapp_enabled')
                                                    ->label('💬 WhatsApp Messages')
                                                    ->default(false)
                                                    ->helperText('Send WhatsApp messages')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('voice_enabled')
                                                    ->label('📞 Voice Calls')
                                                    ->default(false)
                                                    ->helperText('Make automated voice calls')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Workflow Settings')
                                    ->description('Configure workflow behavior and timing')
                                    ->icon('heroicon-m-clock')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('estimated_duration_days')
                                                    ->label('Estimated Duration')
                                                    ->numeric()
                                                    ->suffix('days')
                                                    ->placeholder('7')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Expected workflow completion time'),

                                                Forms\Components\Select::make('access_scope_id')
                                                    ->label('Access Scope')
                                                    ->relationship('accessScope', 'name')
                                                    ->required()
                                                    ->extraAttributes(['class' => 'glass-input'])
                                                    ->helperText('Who can use this template'),

                                                Forms\Components\TagsInput::make('tags')
                                                    ->label('Tags')
                                                    ->separator(',')
                                                    ->placeholder('Add tags...')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('Keywords for template discovery'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s']),
                            ]),

                        Forms\Components\Tabs\Tab::make('Publishing & Permissions')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Forms\Components\Section::make('Publication Status')
                                    ->description('Control template availability and publication')
                                    ->icon('heroicon-m-eye')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Toggle::make('is_published')
                                                    ->label('📢 Published')
                                                    ->helperText('Available for use by tenants')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('✅ Active')
                                                    ->default(true)
                                                    ->helperText('Template is currently active')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\DateTimePicker::make('published_at')
                                                    ->label('Publication Date')
                                                    ->extraInputAttributes(['class' => 'glass-input'])
                                                    ->helperText('When this template was published'),
                                            ])
                                    ])
                                    ->extraAttributes(['class' => 'glass-card animate-fade-in']),

                                Forms\Components\Section::make('Access & Visibility')
                                    ->description('Configure who can view and use this template')
                                    ->icon('heroicon-m-lock-closed')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('is_public')
                                                    ->label('🌍 Public Template')
                                                    ->helperText('Visible to all tenants')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('is_system_template')
                                                    ->label('🔧 System Template')
                                                    ->helperText('Core system template (cannot be deleted)')
                                                    ->extraAttributes(['class' => 'glass-card']),

                                                Forms\Components\Toggle::make('is_customizable')
                                                    ->label('✏️ Allow Customization')
                                                    ->default(true)
                                                    ->helperText('Tenants can modify this template')
                                                    ->extraAttributes(['class' => 'glass-card']),
                                            ])
                                    ]),

                                Forms\Components\Section::make('Advanced Security')
                                    ->description('Lock specific milestones and define restrictions')
                                    ->icon('heroicon-m-shield-exclamation')
                                    ->schema([
                                        Forms\Components\Textarea::make('locked_milestones')
                                            ->label('Locked Milestones (JSON)')
                                            ->rows(3)
                                            ->placeholder('[1, 3, 5]')
                                            ->extraInputAttributes(['class' => 'glass-input'])
                                            ->helperText('Milestone IDs that cannot be modified by tenants')
                                            ->columnSpanFull(),
                                    ])

                            ]),
                    ])
                    ->columnSpanFull()
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
