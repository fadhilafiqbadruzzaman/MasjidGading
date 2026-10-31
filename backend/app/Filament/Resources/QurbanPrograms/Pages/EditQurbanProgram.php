<?php

namespace App\Filament\Resources\QurbanPrograms\Pages;

use App\Filament\Resources\QurbanPrograms\QurbanProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQurbanProgram extends EditRecord
{
    protected static string $resource = QurbanProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
