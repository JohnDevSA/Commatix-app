# Filament Resource Patterns

## Resource Structure

Every Filament Resource should follow this consistent structure:

### 1. Basic Configuration

```php
class ExampleResource extends Resource
{
    protected static ?string $model = Example::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Appropriate Group'; // See MULTI_TENANT_GUIDELINES.md

    protected static ?string $navigationLabel = 'Examples';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';
}
```

### 2. Access Control

Always implement `canAccess()` with proper role checks:

```php
public static function canAccess(): bool
{
    // For super admin only resources
    return auth()->user()?->isSuperAdmin() ?? false;

    // For tenant admin and super admin resources
    return (auth()->user()?->isTenantAdmin() || auth()->user()?->isSuperAdmin()) ?? false;

    // For resources with custom permission
    return auth()->user()?->canManageUsers() ?? false;
}
```

### 3. Query Scoping

**CRITICAL**: Always implement `getEloquentQuery()` for tenant-specific resources:

```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if (!$user) {
        return $query->whereRaw('1 = 0'); // Empty result if no user
    }

    if ($user->isSuperAdmin()) {
        return $query; // Super admins see all
    }

    // Tenant admins see only their tenant's data
    return $query->where('tenant_id', $user->tenant_id);
}
```

## Form Patterns

### Section Organization

Use Tabs for complex forms with multiple logical groups:

```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Tabs::make('Main Content')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Basic Information')
                        ->icon('heroicon-m-information-circle')
                        ->schema([
                            Forms\Components\Section::make('Details')
                                ->description('Primary information')
                                ->icon('heroicon-m-document-text')
                                ->schema([
                                    // Form fields here
                                ])
                                ->extraAttributes(['class' => 'glass-card animate-fade-in']),
                        ]),

                    Forms\Components\Tabs\Tab::make('Advanced Settings')
                        ->icon('heroicon-m-cog-6-tooth')
                        ->schema([
                            // Advanced fields here
                        ]),
                ])
                ->columnSpanFull(),
        ]);
}
```

### Tenant Field Pattern

For tenant selection fields (super admin only):

```php
Forms\Components\Select::make('tenant_id')
    ->label('Associated Tenant')
    ->relationship('tenant', 'name')
    ->required() // or ->nullable() depending on use case
    ->visible(fn () => auth()->user()?->isSuperAdmin())
    ->searchable()
    ->preload()
    ->extraAttributes(['class' => 'glass-input'])
    ->helperText('Select the tenant this record belongs to'),
```

### Relationship Dropdowns (Tenant-Scoped)

**ALWAYS** scope relationship dropdowns by tenant:

```php
Forms\Components\Select::make('division_id')
    ->label('Division')
    ->relationship(
        name: 'division',
        titleAttribute: 'name',
        modifyQueryUsing: fn (Builder $query) =>
            $query->where('tenant_id', auth()->user()->tenant_id)
    )
    ->searchable()
    ->preload()
    ->extraAttributes(['class' => 'glass-input']),
```

### Common Field Patterns

**Text Input with Validation:**
```php
Forms\Components\TextInput::make('name')
    ->required()
    ->maxLength(255)
    ->placeholder('Enter name')
    ->extraInputAttributes(['class' => 'glass-input'])
    ->helperText('Descriptive helper text'),
```

**Email Field:**
```php
Forms\Components\TextInput::make('email')
    ->email()
    ->required()
    ->unique(ignoreRecord: true)
    ->maxLength(255)
    ->extraInputAttributes(['class' => 'glass-input']),
```

**Password Field:**
```php
Forms\Components\TextInput::make('password')
    ->password()
    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
    ->dehydrated(fn ($state) => filled($state))
    ->maxLength(255)
    ->extraInputAttributes(['class' => 'glass-input'])
    ->helperText('Leave empty to keep current password when editing'),
```

**Toggle/Boolean:**
```php
Forms\Components\Toggle::make('is_active')
    ->label('Active')
    ->default(true)
    ->helperText('Enable or disable this record')
    ->extraAttributes(['class' => 'glass-card']),
```

**Date/Time Picker:**
```php
Forms\Components\DateTimePicker::make('starts_at')
    ->label('Start Date')
    ->required()
    ->native(false)
    ->extraAttributes(['class' => 'glass-input']),
```

**Rich Text Editor:**
```php
Forms\Components\RichEditor::make('description')
    ->label('Description')
    ->maxLength(65535)
    ->columnSpanFull()
    ->extraAttributes(['class' => 'glass-input']),
```

## Table Patterns

### Column Layout Options

**Standard Grid Layout:**
```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
            // ... more columns
        ])
        ->contentGrid([
            'md' => 2,
            'xl' => 3,
        ]);
}
```

**Stack Layout (Card-style):**
```php
Tables\Columns\Layout\Stack::make([
    Tables\Columns\TextColumn::make('name')
        ->weight(FontWeight::Bold)
        ->size('lg')
        ->color('primary')
        ->searchable(),

    Tables\Columns\TextColumn::make('email')
        ->icon('heroicon-m-envelope')
        ->iconPosition(IconPosition::Before)
        ->size('sm')
        ->color('gray')
        ->copyable(),
])
    ->space(1)
    ->extraAttributes(['class' => 'glass-card p-2']),
```

### Common Column Patterns

**Badge Column with Color:**
```php
Tables\Columns\TextColumn::make('status')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'active' => 'success',
        'pending' => 'warning',
        'inactive' => 'danger',
        default => 'gray',
    })
    ->icon(fn (string $state): string => match ($state) {
        'active' => 'heroicon-m-check-circle',
        'pending' => 'heroicon-m-clock',
        'inactive' => 'heroicon-m-x-circle',
        default => 'heroicon-m-question-mark-circle',
    }),
```

**Relationship Count:**
```php
Tables\Columns\TextColumn::make('users_count')
    ->label('Users')
    ->counts('users')
    ->sortable()
    ->badge()
    ->color('info'),
```

**Date with Relative Time:**
```php
Tables\Columns\TextColumn::make('created_at')
    ->label('Created')
    ->dateTime('M j, Y')
    ->sortable()
    ->since()
    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s'))
    ->toggleable(),
```

**Conditional Visibility:**
```php
Tables\Columns\TextColumn::make('tenant.name')
    ->label('Tenant')
    ->searchable()
    ->sortable()
    ->visible(fn () => auth()->user()?->isSuperAdmin())
    ->toggleable(),
```

### Filter Patterns

```php
->filters([
    Tables\Filters\SelectFilter::make('status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ])
        ->multiple()
        ->label('Status'),

    Tables\Filters\SelectFilter::make('tenant_id')
        ->relationship('tenant', 'name')
        ->label('Tenant')
        ->multiple()
        ->preload()
        ->visible(fn () => auth()->user()?->isSuperAdmin()),

    Tables\Filters\Filter::make('created_from')
        ->form([
            Forms\Components\DatePicker::make('created_from')
                ->label('Created from'),
        ])
        ->query(fn (Builder $query, array $data): Builder =>
            $query->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
        ),

    Tables\Filters\TrashedFilter::make()
        ->visible(fn () => auth()->user()?->isSuperAdmin()),
])
```

### Action Patterns

**Standard Actions:**
```php
->actions([
    Tables\Actions\ViewAction::make()
        ->label('View')
        ->icon('heroicon-m-eye')
        ->color('info'),

    Tables\Actions\EditAction::make()
        ->label('Edit')
        ->icon('heroicon-m-pencil-square')
        ->color('warning'),

    Tables\Actions\DeleteAction::make()
        ->requiresConfirmation()
        ->modalHeading('Delete record')
        ->modalDescription('Are you sure? This action cannot be undone.'),
])
```

**Custom Action with Form:**
```php
Tables\Actions\Action::make('customAction')
    ->label('Custom Action')
    ->icon('heroicon-o-bolt')
    ->color('success')
    ->form([
        Forms\Components\TextInput::make('parameter')
            ->required()
            ->label('Enter parameter'),
    ])
    ->action(function ($record, array $data) {
        // Perform action

        Notification::make()
            ->title('Action completed')
            ->success()
            ->send();
    })
    ->requiresConfirmation()
    ->modalHeading('Confirm Action')
    ->modalDescription('Please confirm you want to proceed'),
```

### Bulk Action Patterns

```php
->bulkActions([
    Tables\Actions\BulkActionGroup::make([
        Tables\Actions\DeleteBulkAction::make()
            ->requiresConfirmation()
            ->modalHeading('Delete selected records')
            ->modalDescription('Are you sure? This action cannot be undone.'),

        Tables\Actions\BulkAction::make('activate')
            ->label('Activate Selected')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
            ->after(fn () => Notification::make()
                ->title('Records activated successfully')
                ->success()
                ->send()),
    ]),
])
```

## Widget Patterns

### Stats Overview Widget

```php
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExampleStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenantId = auth()->user()->tenant_id;

        return [
            Stat::make('Total Items', $this->getTotalCount($tenantId))
                ->description($this->getGrowthDescription($tenantId))
                ->descriptionIcon($this->getGrowthIcon($tenantId))
                ->color($this->getGrowthColor($tenantId))
                ->chart($this->getChartData($tenantId)),
        ];
    }

    protected function getTotalCount(int|string $tenantId): int
    {
        return Model::where('tenant_id', $tenantId)->count();
    }
}
```

### Chart Widget

```php
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ExampleChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Activity Over Time';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = auth()->user()->tenant_id;
        $months = collect();
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');

            $count = Model::where('tenant_id', $tenantId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months->push($monthLabel);
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Items Created',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // or 'bar', 'pie', etc.
    }
}
```

## Notification Patterns

```php
use Filament\Notifications\Notification;

// Success notification
Notification::make()
    ->title('Operation successful')
    ->body('The record was updated successfully.')
    ->success()
    ->send();

// Error notification
Notification::make()
    ->title('Operation failed')
    ->body('An error occurred while processing your request.')
    ->danger()
    ->send();

// Warning notification
Notification::make()
    ->title('Warning')
    ->body('Please review this action carefully.')
    ->warning()
    ->duration(5000) // milliseconds
    ->send();

// Info notification with action
Notification::make()
    ->title('New update available')
    ->body('Click to view details.')
    ->info()
    ->actions([
        \Filament\Notifications\Actions\Action::make('view')
            ->button()
            ->url(route('updates.show', $update)),
    ])
    ->send();
```

## Styling Conventions

### Glass Morphism Classes

Use these custom classes consistently:
- `glass-card` - For card-style containers
- `glass-input` - For form inputs
- `animate-fade-in` - For fade-in animations
- `animate-slide-up` - For slide-up animations

Add animation delays for staggered effects:
```php
->extraAttributes(['class' => 'glass-card animate-fade-in', 'style' => 'animation-delay: 0.1s'])
```

## Resource Pages

### Custom Page Methods

**List Page:**
```php
class ListExamples extends ListRecords
{
    protected static string $resource = ExampleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

**Create Page:**
```php
class CreateExample extends CreateRecord
{
    protected static string $resource = ExampleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
```

**Edit Page:**
```php
class EditExample extends EditRecord
{
    protected static string $resource = ExampleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

**View Page:**
```php
class ViewExample extends ViewRecord
{
    protected static string $resource = ExampleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
```

## Best Practices

1. **Always scope by tenant** - Use `getEloquentQuery()` and `modifyQueryUsing`
2. **Consistent naming** - Follow Laravel/Filament conventions
3. **Helper text** - Add helpful descriptions to form fields
4. **Icons** - Use Heroicons consistently
5. **Colors** - Use Filament's color system (success, warning, danger, info, gray, primary)
6. **Loading states** - Use `->preload()` on selects for better UX
7. **Search** - Make key columns searchable
8. **Sort** - Make relevant columns sortable
9. **Confirmations** - Require confirmation for destructive actions
10. **Notifications** - Provide feedback after actions