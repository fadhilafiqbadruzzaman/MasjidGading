<?php

namespace App\Filament\Resources\FinancialSummaries\Pages;

use App\Filament\Resources\FinancialSummaries\FinancialSummaryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialSummary extends EditRecord
{
    protected static string $resource = FinancialSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
