<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantSubscriptionResource\Pages;
use App\Models\TenantSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantSubscriptionResource extends Resource
{
    protected static ?string $model = TenantSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Tenant Management';

    protected static ?string $navigationLabel = 'Subscriptions';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('plan_name')
                            ->label('Plan Name')
                            ->options([
                                'trial' => 'Trial',
                                'basic' => 'Basic',
                                'professional' => 'Professional',
                                'enterprise' => 'Enterprise',
                            ])
                            ->required(),
                        Forms\Components\Select::make('billing_interval')
                            ->options([
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'annually' => 'Annually',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('R')
                            ->step(0.01),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'ZAR' => 'South African Rand (ZAR)',
                                'USD' => 'US Dollar (USD)',
                            ])
                            ->required()
                            ->default('ZAR'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'canceled' => 'Canceled',
                                'past_due' => 'Past Due',
                                'trialing' => 'Trialing',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Billing Period')
                    ->schema([
                        Forms\Components\DateTimePicker::make('current_period_start')
                            ->label('Current Period Start'),
                        Forms\Components\DateTimePicker::make('current_period_end')
                            ->label('Current Period End'),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At')
                            ->helperText('Leave empty if not on trial'),
                        Forms\Components\Toggle::make('cancel_at_period_end')
                            ->label('Cancel at Period End')
                            ->helperText('Subscription will not renew at the end of current period'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Provider Integration')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_subscription_id')
                            ->label('Stripe Subscription ID')
                            ->maxLength(255)
                            ->helperText('For international payments'),
                        Forms\Components\TextInput::make('payfast_subscription_id')
                            ->label('PayFast Subscription ID')
                            ->maxLength(255)
                            ->helperText('For South African payments'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->tenant?->name),
                Tables\Columns\TextColumn::make('plan_name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'trial' => 'gray',
                        'basic' => 'info',
                        'professional' => 'warning',
                        'enterprise' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'info',
                        'inactive' => 'warning',
                        'canceled' => 'danger',
                        'past_due' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('currency')
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_interval')
                    ->label('Billing')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_period_start')
                    ->label('Period Start')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Period End')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->date()
                    ->placeholder('No Trial')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('cancel_at_period_end')
                    ->label('Canceling')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stripe_subscription_id')
                    ->label('Stripe ID')
                    ->placeholder('Not Set')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payfast_subscription_id')
                    ->label('PayFast ID')
                    ->placeholder('Not Set')
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
                Tables\Filters\SelectFilter::make('plan_name')
                    ->label('Plan')
                    ->options([
                        'trial' => 'Trial',
                        'basic' => 'Basic',
                        'professional' => 'Professional',
                        'enterprise' => 'Enterprise',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'trialing' => 'Trialing',
                        'inactive' => 'Inactive',
                        'canceled' => 'Canceled',
                        'past_due' => 'Past Due',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->label('Tenant')
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('trial_ending')
                    ->query(fn (Builder $query): Builder => $query->where('trial_ends_at', '<=', now()->addDays(7)))
                    ->label('Trial Ending Soon'),
                Tables\Filters\Filter::make('canceled')
                    ->query(fn (Builder $query): Builder => $query->where('cancel_at_period_end', true))
                    ->label('Canceled Subscriptions'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('extend_trial')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'trialing')
                    ->form([
                        Forms\Components\DatePicker::make('extend_to')
                            ->label('Extend trial to')
                            ->required()
                            ->after('today'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['trial_ends_at' => $data['extend_to']]);
                        \Filament\Notifications\Notification::make()
                            ->title('Trial extended successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTenantSubscriptions::route('/'),
            'create' => Pages\CreateTenantSubscription::route('/create'),
            'edit' => Pages\EditTenantSubscription::route('/{record}/edit'),
        ];
    }
}
