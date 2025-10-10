<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneResource\Pages;
use App\Models\Milestone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Workflow Milestones';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Milestone Information')
                    ->schema([
                        Forms\Components\Select::make('workflow_template_id')
                            ->relationship('workflowTemplate', 'name')
                            ->searchable()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('hint')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('sla_days')
                            ->required()
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Service Level Agreement in days'),
                        Forms\Components\Select::make('status_type_id')
                            ->relationship('statusType', 'name')
                            ->required(),

                        Forms\Components\Toggle::make('requires_approval')
                            ->label('Requires Approval')
                            ->helperText('Whether this milestone requires approval before completion')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => ! $state ? $set('approval_group_name', null) : null),

                        Forms\Components\TextInput::make('approval_group_name')
                            ->label('Approval Group Name')
                            ->helperText('Enter approval group name (Approval groups feature coming soon - will be division-based)')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => $get('requires_approval'))
                            ->placeholder('e.g., Finance Approvers, HR Managers'),

                        Forms\Components\Toggle::make('requires_docs')
                            ->label('Requires Documentation')
                            ->helperText('Whether this milestone requires document attachments')
                            ->reactive(),

                        Forms\Components\Select::make('document_requirements')
                            ->label('Required Documents')
                            ->multiple()
                            ->relationship('documentRequirements', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Select the documents required for this milestone')
                            ->visible(fn (callable $get) => $get('requires_docs'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Actions')
                    ->schema([
                        Forms\Components\Textarea::make('actions')
                            ->label('Actions (JSON)')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Define the actions available at this milestone as JSON'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workflowTemplate.name')
                    ->label('Workflow Template')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->workflowTemplate?->name),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->name),
                Tables\Columns\TextColumn::make('sla_days')
                    ->label('SLA Days')
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
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('requires_docs')
                    ->label('Requires Docs')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Requires Approval')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-mark')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('approval_group_name')
                    ->label('Approval Group')
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hint')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('workflow_template_id')
                    ->relationship('workflowTemplate', 'name')
                    ->label('Workflow Template')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status_type_id')
                    ->relationship('statusType', 'name')
                    ->label('Status Type'),
                Tables\Filters\TernaryFilter::make('requires_docs')
                    ->label('Requires Documentation'),
                Tables\Filters\TernaryFilter::make('requires_approval')
                    ->label('Requires Approval'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray')
                    ->action(function (Milestone $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name . ' (Copy)';
                        $newRecord->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('workflowTemplate.name');
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
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'view' => Pages\ViewMilestone::route('/{record}'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
