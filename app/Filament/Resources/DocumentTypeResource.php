<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Filament\Resources\DocumentTypeResource\RelationManagers;
use App\Models\AccessScope;
use App\Models\DocumentType;
use App\Models\Industry;
use Illuminate\Support\Facades\Cache;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessGlobalResources() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Type Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., FICA Documents, Tax Returns, Invoices'),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Describe the purpose and usage of this document type...')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Access & Scope Configuration')
                    ->schema([
                        Forms\Components\Select::make('access_scope_id')
                            ->label('Access Scope')
                            ->relationship('accessScope', 'label')
                            ->required()
                            ->reactive()
                            ->options(function () {
                                $user = auth()->user();

                                // Super Admin can create all types
                                if ($user && $user->isSuperAdmin()) {
                                    return AccessScope::pluck('label', 'id');
                                }

                                // Regular users can only create tenant-specific types
                                return AccessScope::whereIn('name', ['tenant_custom', 'tenant_shared', 'private'])
                                    ->pluck('label', 'id');
                            })
                            ->helperText('Determines who can access this document type'),


                        Forms\Components\Select::make('industry_category')
                            ->label('Target Industry')
                            ->options(Industry::getDisplayOptions())
                            ->searchable()
                            ->visible(function (callable $get) {
                                $accessScope = AccessScope::find($get('access_scope_id'));
                                return $accessScope && $accessScope->name === 'industry_template';
                            })
                            ->required(function (callable $get) {
                                $accessScope = AccessScope::find($get('access_scope_id'));
                                return $accessScope && $accessScope->name === 'industry_template';
                            })
                            ->helperText('Which industry should have access to this document type'),

                        Forms\Components\Select::make('tenant_id')
                            ->label('Specific Tenant')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->visible(function (callable $get) {
                                $accessScope = AccessScope::find($get('access_scope_id'));
                                return $accessScope && in_array($accessScope->name, ['tenant_custom', 'tenant_shared', 'private']);
                            })
                            ->required(function (callable $get) {
                                $accessScope = AccessScope::find($get('access_scope_id'));
                                return $accessScope && in_array($accessScope->name, ['tenant_custom', 'tenant_shared', 'private']);
                            })
                            ->default(function () {
                                $user = auth()->user();
                                return $user && !$user->isSuperAdmin() ? $user->tenant_id : null;
                            })
                            ->helperText('Which tenant owns this document type'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('allows_multiple')
                            ->label('Allow Multiple Files')
                            ->helperText('Users can upload multiple files of this type')
                            ->default(true),

                        Forms\Components\TextInput::make('max_file_size_mb')
                            ->label('Max File Size (MB)')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(100)
                            ->helperText('Maximum file size allowed in megabytes'),

                        Forms\Components\TagsInput::make('allowed_file_types')
                            ->label('Allowed File Types')
                            ->placeholder('pdf, jpg, png, docx')
                            ->helperText('Enter file extensions (without dots)')
                            ->default(['pdf', 'jpg', 'png', 'docx']),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])->live();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->name),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->tooltip(function (DocumentType $record): ?string {
                        return $record->description;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('accessScope.label')
                    ->label('Access Scope')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Global System' => 'danger',
                        'Industry Template' => 'info',
                        'Tenant Custom' => 'success',
                        'Tenant Shared' => 'warning',
                        'Private' => 'gray',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('industry_category')
                    ->label('Industry')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'financial_services' => '🏦 Financial Services',
                        'healthcare' => '🏥 Healthcare',
                        'education' => '🎓 Education',
                        'real_estate' => '🏘️ Real Estate',
                        'retail' => '🛍️ Retail',
                        'manufacturing' => '🏭 Manufacturing',
                        'construction' => '🏗️ Construction',
                        'hospitality' => '🏨 Hospitality',
                        'logistics' => '🚚 Logistics',
                        'government' => '🏛️ Government',
                        'general' => '🌍 General',
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->placeholder('Global/System')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('access_scope_id')
                    ->label('Access Scope')
                    ->relationship('accessScope', 'label')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('industry_category')
                    ->label('Industry')
                    ->options([
                        'financial_services' => 'Financial Services',
                        'healthcare' => 'Healthcare',
                        'education' => 'Education',
                        'real_estate' => 'Real Estate',
                        'retail' => 'Retail',
                        'manufacturing' => 'Manufacturing',
                        'construction' => 'Construction',
                        'hospitality' => 'Hospitality',
                        'logistics' => 'Logistics',
                        'government' => 'Government',
                        'general' => 'General',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->multiple(),

                Tables\Filters\Filter::make('global_only')
                    ->label('Global/System Only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('tenant_id'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
