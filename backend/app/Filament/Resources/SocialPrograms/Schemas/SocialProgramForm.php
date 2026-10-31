<?php

namespace App\Filament\Resources\SocialPrograms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SocialProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('budget')
                    ->numeric()
                    ->default(null),
                Select::make('status')
                    ->options([
            'planning' => 'Planning',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('active')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
