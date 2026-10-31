<?php

namespace App\Filament\Resources\FacilityReports\Pages;

use App\Filament\Resources\FacilityReports\FacilityReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFacilityReport extends EditRecord
{
    protected static string $resource = FacilityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
