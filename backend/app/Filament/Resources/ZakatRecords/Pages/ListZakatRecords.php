<?php

namespace App\Filament\Resources\ZakatRecords\Pages;

use App\Filament\Resources\ZakatRecords\ZakatRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListZakatRecords extends ListRecords
{
    protected static string $resource = ZakatRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
