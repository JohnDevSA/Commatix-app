<?php

namespace App\Filament\Resources;
use BackedEnum;
use UnitEnum;

use App\Filament\Resources\TaskResource\Pages\CreateTask;
use App\Filament\Resources\TaskResource\Pages\EditTask;
use App\Filament\Resources\TaskResource\Pages\ListTasks;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Filament\Traits\HasGlassmorphicForms;
use App\Filament\Traits\HasSouthAfricanDateFormats;
use App\Models\Subscriber;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowTemplate;
use Carbon\Carbon;
use Deldius\UserField\UserColumn;
use Filament\Actions;
use Filament\Schemas\Components;
use Filament\Forms\Components as FormComponents;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Size;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    use HasGlassmorphicForms;
    use HasSouthAfricanDateFormats;

    protected static ?string $model = Task::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Tasks';

    protected static string | UnitEnum | null $navigationGroup = 'Workflows';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Apply visibility scope based on user role and division
        // Eager load relationships to prevent N+1 queries
        return $query->visibleTo($user)
            ->with([
                'subscriber',
                'workflowTemplate',
                'division',
                'currentMilestone',
                'assignedTo',
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Tabs::make('Task Management')
                    ->tabs([
                        Components\Tabs\Tab::make('Task Details')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->schema([
                                Components\Section::make('Basic Information')
                                    ->description('Define the task title, description, and associated workflow')
                                    ->icon('heroicon-m-information-circle')
                                    ->schema([
                                        Components\Grid::make(1)
                                            ->schema([
                                                FormComponents\TextInput::make('title')
                                                    ->label('Task Title')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('KYC Verification for Client XYZ')
                                                    ->extraInputAttributes(self::glassInput())
                                                    ->columnSpanFull(),

                                                FormComponents\Textarea::make('description')
                                                    ->label('Task Description')
                                                    ->rows(3)
                                                    ->placeholder('Describe the task objectives and requirements...')
                                                    ->extraInputAttributes(self::glassInput())
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCard()),

                                Components\Section::make('Workflow & Subscriber')
                                    ->description('Select the workflow template and associated subscriber')
                                    ->icon('heroicon-m-user-circle')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('workflow_template_id')
                                                    ->label('Workflow Template')
                                                    ->relationship('workflowTemplate', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->reactive()
                                                    ->extraAttributes(self::glassInput())
                                                    ->helperText('Select the workflow process for this task')
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if ($state) {
                                                            $workflow = WorkflowTemplate::find($state);
                                                            if ($workflow && $workflow->estimated_duration_days) {
                                                                $set('due_date', now()->addDays($workflow->estimated_duration_days)->toDateString());
                                                            }
                                                        }
                                                    })
                                                    ->columnSpan(2),

                                                FormComponents\Select::make('subscriber_id')
                                                    ->label('Subscriber')
                                                    ->relationship('subscriber', 'email')
                                                    ->searchable(['email', 'first_name', 'last_name'])
                                                    ->preload()
                                                    ->required()
                                                    ->extraAttributes(self::glassInput())
                                                    ->helperText('Select or create a subscriber for this task')
                                                    ->getOptionLabelFromRecordUsing(fn (Subscriber $record): string => "{$record->first_name} {$record->last_name} ({$record->email})")
                                                    ->createOptionForm([
                                                        FormComponents\Hidden::make('tenant_id')
                                                            ->default(fn () => tenant() ? tenant()->id : null),
                                                        FormComponents\TextInput::make('first_name')
                                                            ->required()
                                                            ->extraInputAttributes(self::glassInput()),
                                                        FormComponents\TextInput::make('last_name')
                                                            ->required()
                                                            ->extraInputAttributes(self::glassInput()),
                                                        FormComponents\TextInput::make('email')
                                                            ->email()
                                                            ->required()
                                                            ->extraInputAttributes(self::glassInput()),
                                                        FormComponents\TextInput::make('phone')
                                                            ->extraInputAttributes(self::glassInput()),
                                                        FormComponents\Select::make('subscriber_list_id')
                                                            ->label('Subscriber List')
                                                            ->relationship('subscriberList', 'name')
                                                            ->required()
                                                            ->extraAttributes(self::glassInput()),
                                                    ])
                                                    ->createOptionUsing(function (array $data): int {
                                                        // Ensure tenant_id is set for new subscribers
                                                        if (tenant()) {
                                                            $data['tenant_id'] = tenant()->id;
                                                        }

                                                        $subscriber = Subscriber::create($data);

                                                        return $subscriber->id;
                                                    })
                                                    ->columnSpan(2),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCardSequence(1)),
                            ]),

                        Components\Tabs\Tab::make('Assignment & Priority')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                Components\Section::make('Task Assignment')
                                    ->description('Assign the task to a user and division')
                                    ->icon('heroicon-m-user-plus')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('assigned_to')
                                                    ->label('Assigned To')
                                                    ->options(User::pluck('name', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->reactive()
                                                    ->extraAttributes(self::glassInput())
                                                    ->helperText('Select the user responsible for this task')
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        // Auto-set division based on assigned user
                                                        if ($state && empty($get('division_id'))) {
                                                            $user = User::find($state);
                                                            if ($user && $user->division_id) {
                                                                $set('division_id', $user->division_id);
                                                            }
                                                        }
                                                    }),

                                                FormComponents\Select::make('division_id')
                                                    ->label('Division')
                                                    ->relationship('division', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->extraAttributes(self::glassInput())
                                                    ->helperText('Will auto-populate based on assigned user'),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCard()),

                                Components\Section::make('Priority & Status')
                                    ->description('Set task priority and current status')
                                    ->icon('heroicon-m-flag')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('priority')
                                                    ->label('Priority Level')
                                                    ->options([
                                                        'low' => '🟢 Low Priority',
                                                        'medium' => '🟡 Medium Priority',
                                                        'high' => '🟠 High Priority',
                                                        'critical' => '🔴 Critical Priority',
                                                    ])
                                                    ->default('medium')
                                                    ->required()
                                                    ->extraAttributes(self::glassInput()),

                                                FormComponents\Select::make('status')
                                                    ->label('Task Status')
                                                    ->options([
                                                        'draft' => 'Draft',
                                                        'scheduled' => 'Scheduled',
                                                        'in_progress' => 'In Progress',
                                                        'on_hold' => 'On Hold',
                                                        'completed' => 'Completed',
                                                        'cancelled' => 'Cancelled',
                                                    ])
                                                    ->default('draft')
                                                    ->required()
                                                    ->extraAttributes(self::glassInput()),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCardSequence(1)),
                            ]),

                        Components\Tabs\Tab::make('Scheduling & Timeline')
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                Components\Section::make('Task Timeline')
                                    ->description('Define start and due dates for this task')
                                    ->icon('heroicon-m-clock')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\DatePicker::make('scheduled_start_date')
                                                    ->label('Scheduled Start Date')
                                                    ->default(now())
                                                    ->required()
                                                    ->reactive()
                                                    ->extraInputAttributes(self::glassInput())
                                                    ->helperText('When this task should begin')
                                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                        // Auto-set status based on start date
                                                        if ($state && Carbon::parse($state)->isFuture()) {
                                                            $set('status', 'scheduled');
                                                        } else {
                                                            $set('status', 'draft');
                                                        }
                                                    }),

                                                FormComponents\DatePicker::make('due_date')
                                                    ->label('Due Date')
                                                    ->extraInputAttributes(self::glassInput())
                                                    ->helperText('Task completion deadline (auto-populated from workflow)'),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCard()),
                            ]),

                        Components\Tabs\Tab::make('Additional Notes')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Components\Section::make('Task Notes')
                                    ->description('Add any additional notes or instructions')
                                    ->icon('heroicon-m-pencil-square')
                                    ->schema([
                                        FormComponents\Textarea::make('notes')
                                            ->label('Notes')
                                            ->rows(6)
                                            ->placeholder('Add any additional notes, requirements, or special instructions...')
                                            ->extraInputAttributes(self::glassInput())
                                            ->columnSpanFull(),
                                    ])
                                    ->extraAttributes(self::glassCard()),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'animate-slide-up']),
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
                UserColumn::make('assigned_to')
                    ->label('Assigned To')
                    ->size(Size::Small)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('division.name')
                    ->label('Division')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('No division'),
                Tables\Columns\TextColumn::make('scheduled_start_date')
                    ->label('Start Date')
                    ->date(self::saDateFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date(self::saDateFormat())
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
                Actions\Action::make('start_early')
                    ->label('Start Early')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (Task $record) => $record->canStartEarly())
                    ->form([
                        FormComponents\Textarea::make('reason')
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

                Actions\Action::make('start_task')
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

                Actions\Action::make('progress_milestone')
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

                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
