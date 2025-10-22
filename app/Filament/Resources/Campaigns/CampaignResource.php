<?php

namespace App\Filament\Resources\Campaigns;

use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Filament\Resources\Campaigns\Pages\ViewCampaign;
use App\Filament\Resources\Campaigns\Schemas\CampaignForm;
use App\Filament\Resources\Campaigns\Schemas\CampaignInfolist;
use App\Filament\Resources\Campaigns\Tables\CampaignsTable;
use App\Models\Campaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static UnitEnum|string|null $navigationGroup = 'Communication Hub';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $draftCount = static::getModel()::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'draft')
            ->count();

        return $draftCount > 0 ? (string) $draftCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        // Scope to current tenant
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['messageTemplate', 'subscriberList', 'creator']);
    }

    public static function form(Schema $schema): Schema
    {
        return CampaignForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampaignInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignsTable::configure($table);
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
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
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
        // Only drafts and scheduled campaigns can be edited
        if (! in_array($record->status, ['draft', 'scheduled'])) {
            return false;
        }

        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    public static function canDelete($record): bool
    {
        // Only drafts can be deleted
        if ($record->status !== 'draft') {
            return false;
        }

        $user = auth()->user();

        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }
}
