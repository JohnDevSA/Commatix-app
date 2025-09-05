<?php

namespace App\Filament\Resources\WorkflowTemplateResource\RelationManagers;

use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $record) {
                                        $this->updateWorkflowDuration();
                                    }),
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
            ->extraAttributes(['x-data' => "{ unsavedChanges: false }"])
            ->extraAttributes(['x-init' => "
                $watch('data', () => {
                    unsavedChanges = true;
                    window.onbeforeunload = () => 'You have unsaved changes. Are you sure you want to leave?';
                });
            "]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sla_days')
                    ->label('SLA')
                    ->numeric()
                    ->suffix(' days')
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 3 => 'success',
                        $state <= 7 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('statusType.name')
                    ->label('Status Type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('requires_docs')
                    ->label('Docs Required')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('hint')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at')
            ->filters([
                Tables\Filters\SelectFilter::make('status_type_id')
                    ->relationship('statusType', 'name')
                    ->label('Status Type'),
                Tables\Filters\TernaryFilter::make('requires_docs')
                    ->label('Requires Documentation'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Milestone')
                    ->icon('heroicon-m-plus-circle')
                    ->modalHeading('Create New Milestone')
                    ->modalSubmitActionLabel('Create Milestone')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Milestone created')
                            ->body('The milestone has been successfully added to this workflow template.')
                    )
                    ->modalWidth('4xl')
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (Milestone $record) => "Edit Milestone: {$record->name}")
                    ->modalSubmitActionLabel('Update Milestone')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Milestone updated')
                            ->body('The milestone has been successfully updated.')
                    )
                    ->modalWidth('4xl')
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Auto-generate UUID if creating
                        if (empty($data['uuid'])) {
                            $data['uuid'] = str()->uuid();
                        }
                        return $data;
                    }),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray')
                    ->action(function (Milestone $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name . ' (Copy)';
                        $newRecord->save();

                        Notification::make()
                            ->success()
                            ->title('Milestone duplicated')
                            ->body("Created a copy: {$newRecord->name}")
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Milestone')
                    ->modalDescription('Are you sure you want to delete this milestone? This action cannot be undone.')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Milestone deleted')
                            ->body('The milestone has been removed from this workflow template.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Milestones')
                        ->modalDescription('Are you sure you want to delete the selected milestones? This action cannot be undone.'),
                ]),
            ])
            ->emptyStateHeading('No milestones yet')
            ->emptyStateDescription('Add milestones to define the workflow steps for this template.')
            ->emptyStateIcon('heroicon-o-flag')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
