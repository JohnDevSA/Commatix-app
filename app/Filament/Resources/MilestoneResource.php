<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneResource\Pages;
use App\Filament\Traits\HasGlassmorphicForms;
use App\Models\Milestone;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class MilestoneResource extends Resource
{
    use HasGlassmorphicForms;

    protected static ?string $model = Milestone::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|UnitEnum|null $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Workflow Milestones';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Tabs::make('Milestone Configuration')
                    ->tabs([
                        Components\Tabs\Tab::make('Basic Information')
                            ->icon('heroicon-m-flag')
                            ->schema([
                                Components\Section::make('Milestone Details')
                                    ->description('Define the milestone name, icon, and associated workflow')
                                    ->icon('heroicon-m-identification')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Select::make('workflow_template_id')
                                                    ->label('Workflow Template')
                                                    ->relationship('workflowTemplate', 'name')
                                                    ->searchable()
                                                    ->required()
                                                    ->extraAttributes(self::glassInput())
                                                    ->columnSpan(2),

                                                FormComponents\Select::make('icon')
                                                    ->label('Icon')
                                                    ->options(\App\Helpers\IconHelper::getMilestoneIcons())
                                                    ->searchable()
                                                    ->allowHtml()
                                                    ->placeholder('Select an icon...')
                                                    ->helperText('Choose an icon that represents this milestone')
                                                    ->extraAttributes(self::glassInput())
                                                    ->columnSpan(1),

                                                FormComponents\TextInput::make('name')
                                                    ->label('Milestone Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('KYC Document Submission')
                                                    ->extraInputAttributes(self::glassInput())
                                                    ->columnSpan(1),

                                                FormComponents\Textarea::make('hint')
                                                    ->label('Hint / Instructions')
                                                    ->rows(3)
                                                    ->placeholder('Provide helpful instructions for this milestone...')
                                                    ->extraInputAttributes(self::glassInput())
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCard()),

                                Components\Section::make('Service Level Agreement')
                                    ->description('Define SLA and status type for this milestone')
                                    ->icon('heroicon-m-clock')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\TextInput::make('sla_days')
                                                    ->label('SLA Duration')
                                                    ->required()
                                                    ->numeric()
                                                    ->suffix('days')
                                                    ->placeholder('3')
                                                    ->helperText('Service Level Agreement in days')
                                                    ->extraInputAttributes(self::glassInput()),

                                                FormComponents\Select::make('status_type_id')
                                                    ->label('Status Type')
                                                    ->relationship('statusType', 'name')
                                                    ->required()
                                                    ->extraAttributes(self::glassInput()),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCardSequence(1)),
                            ]),

                        Components\Tabs\Tab::make('Requirements & Approvals')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Components\Section::make('Approval Configuration')
                                    ->description('Configure approval requirements for this milestone')
                                    ->icon('heroicon-m-user-group')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                FormComponents\Toggle::make('requires_approval')
                                                    ->label('Requires Approval')
                                                    ->helperText('Whether this milestone requires approval before completion')
                                                    ->reactive()
                                                    ->afterStateUpdated(fn ($state, callable $set) => ! $state ? $set('approval_group_name', null) : null)
                                                    ->extraAttributes(self::glassCard('none')),

                                                FormComponents\TextInput::make('approval_group_name')
                                                    ->label('Approval Group Name')
                                                    ->helperText('Enter approval group name (Approval groups feature coming soon - will be division-based)')
                                                    ->maxLength(255)
                                                    ->visible(fn (callable $get) => $get('requires_approval'))
                                                    ->placeholder('e.g., Finance Approvers, HR Managers')
                                                    ->extraInputAttributes(self::glassInput()),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCard()),

                                Components\Section::make('Document Requirements')
                                    ->description('Specify required documentation for this milestone')
                                    ->icon('heroicon-m-document-text')
                                    ->schema([
                                        Components\Grid::make(1)
                                            ->schema([
                                                FormComponents\Toggle::make('requires_docs')
                                                    ->label('Requires Documentation')
                                                    ->helperText('Whether this milestone requires document attachments')
                                                    ->reactive()
                                                    ->extraAttributes(self::glassCard('none')),

                                                FormComponents\Select::make('document_requirements')
                                                    ->label('Required Documents')
                                                    ->multiple()
                                                    ->relationship('documentRequirements', 'name')
                                                    ->preload()
                                                    ->searchable()
                                                    ->helperText('Select the documents required for this milestone')
                                                    ->visible(fn (callable $get) => $get('requires_docs'))
                                                    ->extraAttributes(self::glassInput())
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->extraAttributes(self::glassCardSequence(1)),
                            ]),

                        Components\Tabs\Tab::make('Actions & Workflow')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Components\Section::make('Milestone Actions')
                                    ->description('Define available actions at this milestone stage')
                                    ->icon('heroicon-m-command-line')
                                    ->schema([
                                        FormComponents\Textarea::make('actions')
                                            ->label('Actions Configuration (JSON)')
                                            ->required()
                                            ->rows(8)
                                            ->placeholder('{"approve": "Approve and Continue", "reject": "Reject and Return", "request_info": "Request More Information"}')
                                            ->columnSpanFull()
                                            ->helperText('Define the actions available at this milestone as JSON')
                                            ->extraInputAttributes(self::glassInput()),
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
                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->size('lg')
                    ->default('📌')
                    ->alignCenter(),
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
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('duplicate')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray')
                    ->action(function (Milestone $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name.' (Copy)';
                        $newRecord->save();
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
