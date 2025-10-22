<?php

namespace App\Filament\Resources\MessageTemplates;

use App\Filament\Resources\MessageTemplates\Pages\CreateMessageTemplate;
use App\Filament\Resources\MessageTemplates\Pages\EditMessageTemplate;
use App\Filament\Resources\MessageTemplates\Pages\ListMessageTemplates;
use App\Filament\Resources\MessageTemplates\Pages\ViewMessageTemplate;
use App\Filament\Resources\MessageTemplates\Schemas\MessageTemplateForm;
use App\Filament\Resources\MessageTemplates\Schemas\MessageTemplateInfolist;
use App\Filament\Resources\MessageTemplates\Tables\MessageTemplatesTable;
use App\Models\MessageTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Communication Hub';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $activeCount = static::getModel()::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->count();

        return $activeCount > 0 ? (string) $activeCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        // Scope to current tenant
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['creator']);
    }

    public static function form(Schema $schema): Schema
    {
        return MessageTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MessageTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessageTemplatesTable::configure($table);
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
            'index' => ListMessageTemplates::route('/'),
            'create' => CreateMessageTemplate::route('/create'),
            'view' => ViewMessageTemplate::route('/{record}'),
            'edit' => EditMessageTemplate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    public static function canDelete($record): bool
    {
        // Can't delete templates that are being used in campaigns
        if ($record->campaigns()->exists()) {
            return false;
        }

        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }
}
