<?php

namespace App\Filament\Resources\SocialPrograms\Pages;

use App\Filament\Resources\SocialPrograms\SocialProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSocialProgram extends EditRecord
{
    protected static string $resource = SocialProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
