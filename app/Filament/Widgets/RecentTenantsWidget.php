<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Tenant;

class RecentTenantsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Tenant Activity';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tenant::query()
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tenant Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unique_code')
                    ->label('Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'warning',
                        'suspended' => 'danger',
                        'pending' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subscription_tier')
                    ->label('Plan')
                    ->default('N/A'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
