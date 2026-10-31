<?php

namespace App\Filament\Resources\ProgramAllocations\Pages;

use App\Filament\Resources\ProgramAllocations\ProgramAllocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramAllocations extends ListRecords
{
    protected static string $resource = ProgramAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
