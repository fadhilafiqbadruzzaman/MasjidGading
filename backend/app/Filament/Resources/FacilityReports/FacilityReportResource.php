<?php

namespace App\Filament\Resources\FacilityReports;

use App\Filament\Resources\FacilityReports\Pages\CreateFacilityReport;
use App\Filament\Resources\FacilityReports\Pages\EditFacilityReport;
use App\Filament\Resources\FacilityReports\Pages\ListFacilityReports;
use App\Filament\Resources\FacilityReports\Schemas\FacilityReportForm;
use App\Filament\Resources\FacilityReports\Tables\FacilityReportsTable;
use App\Models\FacilityReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FacilityReportResource extends Resource
{
    protected static ?string $model = FacilityReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    public static function getNavigationGroup(): ?string
    {
        return 'Pemeliharaan & Fasilitas';
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Fasilitas';
    }

    public static function getModelLabel(): string
    {
        return 'Laporan Fasilitas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Laporan Fasilitas';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid', 'marbot']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()?->isMarbot()) {
            $query->where('reported_by', auth()->id());
        }
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return FacilityReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacilityReportsTable::configure($table);
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
            'index' => ListFacilityReports::route('/'),
            'create' => CreateFacilityReport::route('/create'),
            'edit' => EditFacilityReport::route('/{record}/edit'),
        ];
    }
}
