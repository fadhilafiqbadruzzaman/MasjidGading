<?php

namespace App\Filament\Resources\ZakatRecords;

use App\Filament\Resources\ZakatRecords\Pages\CreateZakatRecord;
use App\Filament\Resources\ZakatRecords\Pages\EditZakatRecord;
use App\Filament\Resources\ZakatRecords\Pages\ListZakatRecords;
use App\Filament\Resources\ZakatRecords\Schemas\ZakatRecordForm;
use App\Filament\Resources\ZakatRecords\Tables\ZakatRecordsTable;
use App\Models\ZakatRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ZakatRecordResource extends Resource
{
    protected static ?string $model = ZakatRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    public static function getNavigationGroup(): ?string
    {
        return 'Zakat, Qurban & Bansos';
    }

    public static function getNavigationLabel(): string
    {
        return 'Penerimaan Zakat';
    }

    public static function getModelLabel(): string
    {
        return 'Data Zakat';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penerimaan Zakat';
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
        return ZakatRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ZakatRecordsTable::configure($table);
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
            'index' => ListZakatRecords::route('/'),
            'create' => CreateZakatRecord::route('/create'),
            'edit' => EditZakatRecord::route('/{record}/edit'),
        ];
    }
}
