<?php

namespace App\Filament\Resources\SocialPrograms;

use App\Filament\Resources\SocialPrograms\Pages\CreateSocialProgram;
use App\Filament\Resources\SocialPrograms\Pages\EditSocialProgram;
use App\Filament\Resources\SocialPrograms\Pages\ListSocialPrograms;
use App\Filament\Resources\SocialPrograms\Schemas\SocialProgramForm;
use App\Filament\Resources\SocialPrograms\Tables\SocialProgramsTable;
use App\Models\SocialProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SocialProgramResource extends Resource
{
    protected static ?string $model = SocialProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getNavigationGroup(): ?string
    {
        return 'Zakat, Qurban & Bansos';
    }

    public static function getNavigationLabel(): string
    {
        return 'Bantuan Sosial';
    }

    public static function getModelLabel(): string
    {
        return 'Program Bansos';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bantuan Sosial';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid']);
    }

    public static function form(Schema $schema): Schema
    {
        return SocialProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialProgramsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\SocialPrograms\RelationManagers\ApplicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocialPrograms::route('/'),
            'create' => CreateSocialProgram::route('/create'),
            'edit' => EditSocialProgram::route('/{record}/edit'),
        ];
    }
}
