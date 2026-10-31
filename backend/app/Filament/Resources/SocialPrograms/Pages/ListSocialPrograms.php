<?php

namespace App\Filament\Resources\SocialPrograms\Pages;

use App\Filament\Resources\SocialPrograms\SocialProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSocialPrograms extends ListRecords
{
    protected static string $resource = SocialProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
