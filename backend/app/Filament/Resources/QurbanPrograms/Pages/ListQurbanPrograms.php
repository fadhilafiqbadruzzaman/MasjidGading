<?php

namespace App\Filament\Resources\QurbanPrograms\Pages;

use App\Filament\Resources\QurbanPrograms\QurbanProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQurbanPrograms extends ListRecords
{
    protected static string $resource = QurbanProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
