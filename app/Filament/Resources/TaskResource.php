<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages\CreateTask;
use App\Filament\Resources\TaskResource\Pages\EditTask;
use App\Filament\Resources\TaskResource\Pages\ListTasks;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Models\Subscriber;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowTemplate;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?string $navigationGroup = 'Workflows';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query; // Super admins see all tasks
        }

        // Tenant users only see tasks from their tenant
        return $query->where('tenant_id', $user->tenant_id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('workflow_template_id')
                            ->label('Workflow Template')
                            ->relationship('workflowTemplate', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $workflow = WorkflowTemplate::find($state);
                                    if ($workflow && $workflow->estimated_duration_days) {
                                        $set('due_date', now()->addDays($workflow->estimated_duration_days)->toDateString());
                                    }
                                }
                            }),
                        Forms\Components\Select::make('subscriber_id')
                            ->label('Subscriber')
                            ->relationship('subscriber', 'email')
                            ->searchable(['email', 'first_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (Subscriber $record): string => "{$record->first_name} {$record->last_name} ({$record->email})")
                            ->createOptionForm([
                                Forms\Components\Hidden::make('tenant_id')
                                    ->default(fn () => tenant() ? tenant()->id : null),
                                Forms\Components\TextInput::make('first_name')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone'),
                                Forms\Components\Select::make('subscriber_list_id')
                                    ->label('Subscriber List')
                                    ->relationship('subscriberList', 'name')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                // Ensure tenant_id is set for new subscribers
                                if (tenant()) {
                                    $data['tenant_id'] = tenant()->id;
                                }

                                $subscriber = Subscriber::create($data);

                                return $subscriber->id;
                            }),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Assignment & Scheduling')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assigned To')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'critical' => 'Critical',
                            ])
                            ->default('medium')
                            ->required(),
                        Forms\Components\DatePicker::make('scheduled_start_date')
                            ->label('Scheduled Start Date')
                            ->default(now())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Auto-set status based on start date
                                if ($state && Carbon::parse($state)->isFuture()) {
                                    $set('status', 'scheduled');
                                } else {
                                    $set('status', 'draft');
                                }
                            }),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'in_progress' => 'In Progress',
                                'on_hold' => 'On Hold',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->title),
                Tables\Columns\TextColumn::make('subscriber.email')
                    ->label('Subscriber')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => "{$record->subscriber->first_name} {$record->subscriber->last_name}")
                    ->limit(25)
                    ->tooltip(fn ($record) => "{$record->subscriber->first_name} {$record->subscriber->last_name}"),
                Tables\Columns\TextColumn::make('workflowTemplate.name')
                    ->label('Workflow')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'info',
                        'in_progress' => 'warning',
                        'on_hold' => 'danger',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('scheduled_start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('currentMilestone.name')
                    ->label('Current Step')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'on_hold' => 'On Hold',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'critical' => 'Critical',
                    ]),
                Tables\Filters\Filter::make('due_soon')
                    ->label('Due This Week')
                    ->query(fn ($query) => $query->whereBetween('due_date', [now(), now()->addWeek()])),
            ])
            ->actions([
                Tables\Actions\Action::make('start_early')
                    ->label('Start Early')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (Task $record) => $record->canStartEarly())
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for Early Start')
                            ->required()
                            ->helperText('Please provide a reason for starting this task before the scheduled date'),
                    ])
                    ->action(function (Task $record, array $data) {
                        if ($record->startTask($data['reason'])) {
                            Notification::make()
                                ->title('Task Started Early')
                                ->body('The task has been started before the scheduled date.')
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('start_task')
                    ->label('Start Task')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->visible(fn (Task $record) => $record->shouldAutoStart())
                    ->action(function (Task $record) {
                        if ($record->startTask()) {
                            Notification::make()
                                ->title('Task Started')
                                ->body('The task has been started and is now in progress.')
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('progress_milestone')
                    ->label('Progress')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('warning')
                    ->visible(fn (Task $record) => $record->status === 'in_progress' && $record->canProgress())
                    ->requiresConfirmation()
                    ->modalHeading('Progress to Next Milestone')
                    ->modalDescription('Are you sure you want to move this task to the next milestone?')
                    ->action(function (Task $record) {
                        // Using the service directly for demonstration
                        $progressionService = app(\App\Contracts\Services\TaskProgressionInterface::class);

                        try {
                            $nextMilestone = $progressionService->progressToNextMilestone($record, auth()->user());

                            if ($nextMilestone) {
                                Notification::make()
                                    ->title('Milestone Progressed')
                                    ->body("Task moved to: {$nextMilestone->milestone->name}")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Task Completed!')
                                    ->body('The task has been completed successfully.')
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Progress Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'view' => ViewTask::route('/{record}'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
