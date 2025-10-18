<?php

namespace App\Filament\Resources;
use BackedEnum;
use UnitEnum;

use App\Contracts\Services\TaskSchedulingInterface;
use App\Filament\Resources\SubscriberListResource\Pages;
use App\Models\SubscriberList;
use App\Models\User;
use App\Models\WorkflowTemplate;
use Filament\Schemas\Components;
use Filament\Forms\Components as FormComponents;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use App\Filament\Traits\HasGlassmorphicForms;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriberListResource extends Resource
{
    use HasGlassmorphicForms;
    protected static ?string $model = SubscriberList::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-queue-list';

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = 'Subscriber Lists';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return (auth()->user()?->isTenantAdmin() || auth()->user()?->isSuperAdmin()) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tenant_id', auth()->user()->tenant_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('List Information')
                    ->schema([
                        FormComponents\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Name of the subscriber list'),

                        FormComponents\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Brief description of this list'),

                        FormComponents\Toggle::make('is_public')
                            ->label('Public List')
                            ->helperText('Make this list available for public signup forms')
                            ->default(false),
                    ])->columns(2),
            ]);
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

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_subscribers')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('active_subscribers')
                    ->label('Active')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s'))
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Public Lists')
                    ->placeholder('All lists')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),

                Actions\Action::make('scheduleTasksAction')
                    ->label('Schedule Tasks')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->form([
                        FormComponents\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Follow-up Call'),

                        FormComponents\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Task description'),

                        FormComponents\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium')
                            ->required(),

                        FormComponents\Select::make('workflow_template_id')
                            ->label('Workflow Template (Optional)')
                            ->options(function () {
                                return WorkflowTemplate::where('tenant_id', auth()->user()->tenant_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable(),

                        FormComponents\DateTimePicker::make('scheduled_start_date')
                            ->label('Start Date')
                            ->default(now()),

                        FormComponents\DateTimePicker::make('due_date')
                            ->label('Due Date'),

                        FormComponents\Select::make('user_ids')
                            ->label('Assign to Users (Optional)')
                            ->multiple()
                            ->options(function () {
                                return User::where('tenant_id', auth()->user()->tenant_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->helperText('If multiple users selected, tasks will be distributed using round-robin'),
                    ])
                    ->action(function (SubscriberList $record, array $data) {
                        $schedulingService = app(TaskSchedulingInterface::class);

                        $taskData = [
                            'title' => $data['title'],
                            'description' => $data['description'] ?? '',
                            'priority' => $data['priority'],
                            'status' => 'scheduled',
                            'workflow_template_id' => $data['workflow_template_id'] ?? null,
                            'scheduled_start_date' => $data['scheduled_start_date'] ?? now(),
                            'due_date' => $data['due_date'] ?? null,
                        ];

                        $users = null;
                        if (! empty($data['user_ids'])) {
                            $users = User::whereIn('id', $data['user_ids'])->get();
                        }

                        try {
                            $tasks = $schedulingService->scheduleTasksForSubscribers(
                                $record,
                                $taskData,
                                $users
                            );

                            Notification::make()
                                ->success()
                                ->title('Tasks Scheduled Successfully')
                                ->body("Created {$tasks->count()} tasks for {$record->name}")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error Scheduling Tasks')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriberLists::route('/'),
            'create' => Pages\CreateSubscriberList::route('/create'),
            'edit' => Pages\EditSubscriberList::route('/{record}/edit'),
        ];
    }
}
