<?php

namespace App\Filament\Resources\QurbanPrograms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QurbanProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
            'open' => 'Open',
            'closed' => 'Closed',
            'distributed' => 'Distributed',
            'completed' => 'Completed',
        ])
                    ->default('open')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
