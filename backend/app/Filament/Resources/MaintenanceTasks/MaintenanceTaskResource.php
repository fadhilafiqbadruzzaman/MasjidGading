<?php

namespace App\Filament\Resources\MaintenanceTasks;

use App\Filament\Resources\MaintenanceTasks\Pages\CreateMaintenanceTask;
use App\Filament\Resources\MaintenanceTasks\Pages\EditMaintenanceTask;
use App\Filament\Resources\MaintenanceTasks\Pages\ListMaintenanceTasks;
use App\Filament\Resources\MaintenanceTasks\Schemas\MaintenanceTaskForm;
use App\Filament\Resources\MaintenanceTasks\Tables\MaintenanceTasksTable;
use App\Models\MaintenanceTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceTaskResource extends Resource
{
    protected static ?string $model = MaintenanceTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function getNavigationGroup(): ?string
    {
        return 'Pemeliharaan & Fasilitas';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tugas Marbot';
    }

    public static function getModelLabel(): string
    {
        return 'Tugas Pemeliharaan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tugas Marbot';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_masjid', 'marbot']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()?->isMarbot()) {
            $query->where('assigned_to', auth()->id());
        }
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return MaintenanceTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceTasksTable::configure($table);
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
            'index' => ListMaintenanceTasks::route('/'),
            'create' => CreateMaintenanceTask::route('/create'),
            'edit' => EditMaintenanceTask::route('/{record}/edit'),
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
