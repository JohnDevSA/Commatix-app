<?php

namespace App\Filament\Resources\WorkflowTemplateResource\RelationManagers;

use Filament\Actions;
use Filament\Schemas\Components;
use Filament\Forms\Components as FormComponents;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Traits\HasGlassmorphicForms;
use Filament\Tables;
use Filament\Tables\Table;

class MilestonesRelationManager extends RelationManager
{
    use HasGlassmorphicForms;

    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Workflow Milestones';

    protected static ?string $modelLabel = 'milestone';

    protected static ?string $pluralModelLabel = 'milestones';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Milestone Details')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                FormComponents\Select::make('icon')
                                    ->label('Icon')
                                    ->options(\App\Helpers\IconHelper::getMilestoneIcons())
                                    ->searchable()
                                    ->allowHtml()
                                    ->placeholder('Select an icon...')
                                    ->helperText('Choose an icon for this milestone')
                                    ->columnSpan(1),
                                FormComponents\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->columnSpan(1),
                                FormComponents\TextInput::make('sequence_order')
                                    ->numeric()
                                    ->required()
                                    ->default(fn () => $this->getOwnerRecord()->milestones()->max('sequence_order') + 1)
                                    ->minValue(1),
                                FormComponents\Textarea::make('description')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Components\Section::make('Duration & Requirements')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                FormComponents\TextInput::make('estimated_duration_days')
                                    ->label('Duration (Days)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->live(onBlur: true),
                                FormComponents\Select::make('milestone_type')
                                    ->options([
                                        'task' => 'Task Milestone',
                                        'approval' => 'Approval Gate',
                                        'documentation' => 'Documentation',
                                        'review' => 'Review Point',
                                        'notification' => 'Notification',
                                    ])
                                    ->required(),
                                FormComponents\Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'critical' => 'Critical',
                                    ])
                                    ->default('medium'),
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                FormComponents\Toggle::make('requires_approval')
                                    ->label('Requires Approval')
                                    ->default(false)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => ! $state ? $set('approval_group_name', null) : null),
                                FormComponents\Toggle::make('requires_docs')
                                    ->label('Requires Documentation')
                                    ->default(false)
                                    ->reactive(),
                                FormComponents\Toggle::make('can_be_skipped')
                                    ->label('Can be Skipped')
                                    ->default(false),
                                FormComponents\Toggle::make('auto_complete')
                                    ->label('Auto-complete when conditions met')
                                    ->default(false),
                            ]),

                        FormComponents\TextInput::make('approval_group_name')
                            ->label('Approval Group Name')
                            ->helperText('Enter approval group name (Approval groups feature coming soon - division-based)')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => $get('requires_approval') === true)
                            ->placeholder('e.g., Finance Approvers, HR Managers')
                            ->columnSpanFull(),

                        FormComponents\Select::make('document_requirements')
                            ->label('Required Documents')
                            ->multiple()
                            ->relationship('documentRequirements', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Select the documents required for this milestone')
                            ->visible(fn (callable $get) => $get('requires_docs') === true)
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Actions & Notifications')
                    ->schema([
                        FormComponents\Textarea::make('completion_criteria')
                            ->label('Completion Criteria')
                            ->helperText('What needs to be done to complete this milestone?')
                            ->rows(2),
                        FormComponents\TagsInput::make('actions')
                            ->label('Associated Actions')
                            ->helperText('Actions to be triggered when milestone is reached'),
                        FormComponents\Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(2),
                    ]),
            ])
            ->live()
            ->extraAttributes(['onbeforeunload' => "return 'You have unsaved changes. Are you sure you want to leave?'"]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('sequence_order')
                    ->label('Order')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->size('lg')
                    ->default('📌')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('milestone_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'task' => 'success',
                        'approval' => 'warning',
                        'documentation' => 'info',
                        'review' => 'primary',
                        'notification' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('estimated_duration_days')
                    ->label('Duration')
                    ->suffix(' days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('requires_approval')
                    ->boolean()
                    ->label('Approval'),
                Tables\Columns\IconColumn::make('requires_docs')
                    ->boolean()
                    ->label('Docs'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('milestone_type')
                    ->options([
                        'task' => 'Task',
                        'approval' => 'Approval',
                        'documentation' => 'Documentation',
                        'review' => 'Review',
                        'notification' => 'Notification',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'critical' => 'Critical',
                    ]),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['workflow_template_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->after(function () {
                        $this->updateWorkflowDuration();
                    }),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->after(function () {
                        $this->updateWorkflowDuration();
                    }),
                Actions\DeleteAction::make()
                    ->after(function () {
                        $this->updateWorkflowDuration();
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->updateWorkflowDuration();
                        }),
                ]),
            ])
            ->defaultSort('sequence_order');
    }

    private function updateWorkflowDuration(): void
    {
        $totalDuration = $this->getOwnerRecord()->milestones()->sum('estimated_duration_days');
        $this->getOwnerRecord()->update(['estimated_duration_days' => $totalDuration]);

        Notification::make()
            ->title('Workflow Duration Updated')
            ->body("Total estimated duration: {$totalDuration} days")
            ->info()
            ->send();
    }
}
