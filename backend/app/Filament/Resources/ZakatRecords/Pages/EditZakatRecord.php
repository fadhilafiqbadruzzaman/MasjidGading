<?php

namespace App\Filament\Resources\ZakatRecords\Pages;

use App\Filament\Resources\ZakatRecords\ZakatRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditZakatRecord extends EditRecord
{
    protected static string $resource = ZakatRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
