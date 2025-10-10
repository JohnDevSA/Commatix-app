<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantWorkflowTemplateResource\Pages\CreateTenantWorkflowTemplate;
use App\Filament\Resources\TenantWorkflowTemplateResource\Pages\EditTenantWorkflowTemplate;
use App\Filament\Resources\TenantWorkflowTemplateResource\Pages\ListTenantWorkflowTemplates;
use App\Filament\Resources\TenantWorkflowTemplateResource\Pages\ViewTenantWorkflowTemplate;
use App\Filament\Resources\TenantWorkflowTemplateResource\RelationManagers\MilestonesRelationManager;
use App\Models\WorkflowTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TenantWorkflowTemplateResource extends Resource
{
    protected static ?string $model = WorkflowTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Workflow Templates';

    protected static ?string $navigationGroup = 'Workflows';

    protected static ?string $slug = 'tenant-workflow-templates';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->canManageWorkflows() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3),
                        Forms\Components\Select::make('industry_category')
                            ->options(\App\Models\Industry::getDisplayOptions())
                            ->label('Industry')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('estimated_duration_days')
                            ->label('Estimated Duration (Days)')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Hidden::make('access_scope_id')
                            ->default(function () {
                                $scope = \App\Models\AccessScope::where('name', 'tenant_custom')->first();

                                return $scope ? $scope->id : 1; // Fallback to ID 1 if not found
                            }),
                        Forms\Components\Hidden::make('template_type')
                            ->default('custom'),
                        Forms\Components\Hidden::make('created_by')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Hidden::make('is_public')
                            ->default(false),
                        Forms\Components\Hidden::make('is_system_template')
                            ->default(false),
                        Forms\Components\Hidden::make('template_version')
                            ->default('1.0'),
                        Forms\Components\Select::make('complexity_level')
                            ->options([
                                'simple' => 'Simple',
                                'medium' => 'Medium',
                                'complex' => 'Complex',
                            ])
                            ->default('medium')
                            ->required(),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantWorkflowTemplates::route('/'),
            'create' => CreateTenantWorkflowTemplate::route('/create'),
            'view' => ViewTenantWorkflowTemplate::route('/{record}'),
            'edit' => EditTenantWorkflowTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        $tenant = tenant();

        return parent::getEloquentQuery()
            ->where(function ($query) use ($tenant, $user) {
                // Show published system and industry templates
                $query->where('is_published', true)
                    ->whereIn('template_type', ['system', 'industry'])
                    ->where('is_public', true);

                // Show user's own custom templates
                $query->orWhere(function ($subQuery) use ($user) {
                    $subQuery->where('template_type', 'custom')
                        ->where('created_by', $user->id);
                });

                // Show templates matching tenant's industry
                if ($tenant && $tenant->industry_classification) {
                    $query->orWhere('industry_category', $tenant->industry_classification);
                }
            })
            ->with(['milestones', 'parentTemplate'])
            ->withCount('milestones');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->name),

                Tables\Columns\TextColumn::make('template_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'system' => 'danger',
                        'industry' => 'info',
                        'custom' => 'warning',
                        'copied' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('industry_category')
                    ->label('Industry')
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::title(str_replace('_', ' ', $state)) : 'N/A')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('milestones_count')
                    ->label('Milestones')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('estimated_duration_days')
                    ->label('Duration')
                    ->suffix(' days')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('template_type')
                    ->options([
                        'system' => 'System Templates',
                        'industry' => 'Industry Templates',
                        'custom' => 'My Custom Templates',
                        'copied' => 'Copied Templates',
                    ]),

                Tables\Filters\SelectFilter::make('industry_category')
                    ->options([
                        'financial_services' => 'Financial Services',
                        'healthcare' => 'Healthcare',
                        'education' => 'Education',
                        'real_estate' => 'Real Estate',
                        'retail' => 'Retail',
                        'manufacturing' => 'Manufacturing',
                        'construction' => 'Construction',
                        'hospitality' => 'Hospitality',
                        'logistics' => 'Logistics',
                        'government' => 'Government',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('copy')
                    ->label('Use Template')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(fn (WorkflowTemplate $record) => $record->template_type !== 'custom' || $record->created_by !== auth()->id())
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->default(fn (WorkflowTemplate $record) => $record->name.' (Custom)')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->default(fn (WorkflowTemplate $record) => $record->description)
                            ->rows(3),
                        Forms\Components\Toggle::make('copy_milestones')
                            ->label('Copy all milestones')
                            ->default(true),
                    ])
                    ->action(function (WorkflowTemplate $record, array $data) {
                        $newWorkflow = $record->replicate();
                        $newWorkflow->fill([
                            'name' => $data['name'],
                            'description' => $data['description'],
                            'template_type' => 'custom',
                            'parent_template_id' => $record->id,
                            'created_by' => auth()->id(),
                            'is_published' => false,
                            'is_public' => false,
                            'is_system_template' => false,
                            'template_version' => '1.0',
                            'complexity_level' => 'simple',
                            'estimated_duration_days' => $record->estimated_duration_days ?? 0,
                            // Set access_scope_id for tenant custom workflows
                            'access_scope_id' => \App\Models\AccessScope::where('name', 'tenant_custom')->first()?->id,
                        ]);

                        if (tenant()) {
                            $newWorkflow->tenant_id = tenant()->id;
                        }

                        $newWorkflow->save();

                        if ($data['copy_milestones']) {
                            foreach ($record->milestones as $milestone) {
                                $newMilestone = $milestone->replicate();
                                $newMilestone->workflow_template_id = $newWorkflow->id;
                                $newMilestone->save();
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Template Copied Successfully')
                            ->body("'{$data['name']}' has been created and is ready for editing.")
                            ->success()
                            ->send();

                        // Optionally refresh the table to show the new workflow
                        $this->dispatch('refresh');
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (WorkflowTemplate $record) => $record->created_by === auth()->id()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MilestonesRelationManager::class,
        ];
    }
}
