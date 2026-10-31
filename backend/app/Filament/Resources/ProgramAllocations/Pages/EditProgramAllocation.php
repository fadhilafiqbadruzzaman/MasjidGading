<?php

namespace App\Filament\Resources\ProgramAllocations\Pages;

use App\Filament\Resources\ProgramAllocations\ProgramAllocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramAllocation extends EditRecord
{
    protected static string $resource = ProgramAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
