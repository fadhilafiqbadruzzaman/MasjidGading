<?php

namespace App\Filament\Resources\FacilityReports\Pages;

use App\Filament\Resources\FacilityReports\FacilityReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFacilityReports extends ListRecords
{
    protected static string $resource = FacilityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
