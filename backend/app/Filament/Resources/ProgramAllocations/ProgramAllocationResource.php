<?php

namespace App\Filament\Resources\ProgramAllocations;

use App\Filament\Resources\ProgramAllocations\Pages\CreateProgramAllocation;
use App\Filament\Resources\ProgramAllocations\Pages\EditProgramAllocation;
use App\Filament\Resources\ProgramAllocations\Pages\ListProgramAllocations;
use App\Filament\Resources\ProgramAllocations\Schemas\ProgramAllocationForm;
use App\Filament\Resources\ProgramAllocations\Tables\ProgramAllocationsTable;
use App\Models\ProgramAllocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProgramAllocationResource extends Resource
{
    protected static ?string $model = ProgramAllocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getNavigationGroup(): ?string
    {
        return 'Keuangan & Donasi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jejak Amanah';
    }

    public static function getModelLabel(): string
    {
        return 'Alokasi Program';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jejak Amanah';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid']);
    }

    public static function form(Schema $schema): Schema
    {
        return ProgramAllocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramAllocationsTable::configure($table);
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
            'index' => ListProgramAllocations::route('/'),
            'create' => CreateProgramAllocation::route('/create'),
            'edit' => EditProgramAllocation::route('/{record}/edit'),
        ];
    }
}
