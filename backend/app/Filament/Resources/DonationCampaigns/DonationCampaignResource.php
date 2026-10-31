<?php

namespace App\Filament\Resources\DonationCampaigns;

use App\Filament\Resources\DonationCampaigns\Pages\CreateDonationCampaign;
use App\Filament\Resources\DonationCampaigns\Pages\EditDonationCampaign;
use App\Filament\Resources\DonationCampaigns\Pages\ListDonationCampaigns;
use App\Filament\Resources\DonationCampaigns\Schemas\DonationCampaignForm;
use App\Filament\Resources\DonationCampaigns\Tables\DonationCampaignsTable;
use App\Models\DonationCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationCampaignResource extends Resource
{
    protected static ?string $model = DonationCampaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    public static function getNavigationGroup(): ?string
    {
        return 'Keuangan & Donasi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Program Donasi';
    }

    public static function getModelLabel(): string
    {
        return 'Program Donasi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Program Donasi';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid']);
    }

    public static function form(Schema $schema): Schema
    {
        return DonationCampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationCampaignsTable::configure($table);
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
            'index' => ListDonationCampaigns::route('/'),
            'create' => CreateDonationCampaign::route('/create'),
            'edit' => EditDonationCampaign::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
