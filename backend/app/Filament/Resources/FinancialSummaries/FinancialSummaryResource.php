<?php

namespace App\Filament\Resources\FinancialSummaries;

use App\Filament\Resources\FinancialSummaries\Pages\CreateFinancialSummary;
use App\Filament\Resources\FinancialSummaries\Pages\EditFinancialSummary;
use App\Filament\Resources\FinancialSummaries\Pages\ListFinancialSummaries;
use App\Filament\Resources\FinancialSummaries\Schemas\FinancialSummaryForm;
use App\Filament\Resources\FinancialSummaries\Tables\FinancialSummariesTable;
use App\Models\FinancialSummary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinancialSummaryResource extends Resource
{
    protected static ?string $model = FinancialSummary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): ?string
    {
        return 'Keuangan & Donasi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Keuangan';
    }

    public static function getModelLabel(): string
    {
        return 'Laporan Keuangan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Laporan Keuangan';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid']);
    }

    public static function form(Schema $schema): Schema
    {
        return FinancialSummaryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialSummariesTable::configure($table);
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
            'index' => ListFinancialSummaries::route('/'),
            'create' => CreateFinancialSummary::route('/create'),
            'edit' => EditFinancialSummary::route('/{record}/edit'),
        ];
    }
}
