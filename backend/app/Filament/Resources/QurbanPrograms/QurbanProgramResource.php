<?php

namespace App\Filament\Resources\QurbanPrograms;

use App\Filament\Resources\QurbanPrograms\Pages\CreateQurbanProgram;
use App\Filament\Resources\QurbanPrograms\Pages\EditQurbanProgram;
use App\Filament\Resources\QurbanPrograms\Pages\ListQurbanPrograms;
use App\Filament\Resources\QurbanPrograms\Schemas\QurbanProgramForm;
use App\Filament\Resources\QurbanPrograms\Tables\QurbanProgramsTable;
use App\Models\QurbanProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QurbanProgramResource extends Resource
{
    protected static ?string $model = QurbanProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    public static function getNavigationGroup(): ?string
    {
        return 'Zakat, Qurban & Bansos';
    }

    public static function getNavigationLabel(): string
    {
        return 'Program Qurban';
    }

    public static function getModelLabel(): string
    {
        return 'Program Qurban';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Program Qurban';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid']);
    }

    public static function form(Schema $schema): Schema
    {
        return QurbanProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QurbanProgramsTable::configure($table);
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
            'index' => ListQurbanPrograms::route('/'),
            'create' => CreateQurbanProgram::route('/create'),
            'edit' => EditQurbanProgram::route('/{record}/edit'),
        ];
    }
}
