<?php

namespace App\Filament\Resources\FinancialSummaries\Pages;

use App\Filament\Resources\FinancialSummaries\FinancialSummaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinancialSummaries extends ListRecords
{
    protected static string $resource = FinancialSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
