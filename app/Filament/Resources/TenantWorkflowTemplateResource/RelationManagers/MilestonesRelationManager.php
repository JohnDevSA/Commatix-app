<?php

namespace App\Filament\Resources\TenantWorkflowTemplateResource\RelationManagers;

use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Workflow Milestones';

    protected static ?string $modelLabel = 'milestone';

    protected static ?string $pluralModelLabel = 'milestones';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Milestone Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('sequence_order')
                                    ->numeric()
                                    ->required()
                                    ->default(fn () => $this->getOwnerRecord()->milestones()->max('sequence_order') + 1)
                                    ->minValue(1),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Forms\Components\Section::make('Duration & Requirements')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('estimated_duration_days')
                                    ->label('Duration (Days)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->live(onBlur: true),
                                Forms\Components\Select::make('milestone_type')
                                    ->options([
                                        'task' => 'Task Milestone',
                                        'approval' => 'Approval Gate',
                                        'documentation' => 'Documentation',
                                        'review' => 'Review Point',
                                        'notification' => 'Notification',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'critical' => 'Critical',
                                    ])
                                    ->default('medium'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('requires_approval')
                                    ->label('Requires Approval')
                                    ->default(false),
                                Forms\Components\Toggle::make('requires_docs')
                                    ->label('Requires Documentation')
                                    ->default(false),
                                Forms\Components\Toggle::make('can_be_skipped')
                                    ->label('Can be Skipped')
                                    ->default(false),
                                Forms\Components\Toggle::make('auto_complete')
                                    ->label('Auto-complete when conditions met')
                                    ->default(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Actions & Notifications')
                    ->schema([
                        Forms\Components\Textarea::make('completion_criteria')
                            ->label('Completion Criteria')
                            ->helperText('What needs to be done to complete this milestone?')
                            ->rows(2),
                        Forms\Components\TagsInput::make('actions')
                            ->label('Associated Actions')
                            ->helperText('Actions to be triggered when milestone is reached'),
                        Forms\Components\Textarea::make('notes')
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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['workflow_template_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    })
                    ->after(function () {
                        $this->updateWorkflowDuration();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        $this->updateWorkflowDuration();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        $this->updateWorkflowDuration();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->updateWorkflowDuration();
                        }),
                ]),
            ])
            ->defaultSort('sequence_order');
    }

    protected function updateWorkflowDuration(): void
    {
        $workflow = $this->getOwnerRecord();
        $totalDuration = $workflow->milestones()->sum('estimated_duration_days');

        $workflow->update([
            'estimated_duration_days' => $totalDuration
        ]);

        Notification::make()
            ->title('Workflow Duration Updated')
            ->body("Total estimated duration: {$totalDuration} days")
            ->info()
            ->send();
    }
}
