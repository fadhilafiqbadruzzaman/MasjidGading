<?php

namespace App\Filament\Resources\ProgramAllocations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProgramAllocationForm
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
                TextInput::make('total_allocated')
                    ->required()
                    ->numeric(),
                TextInput::make('campaign_id')
                    ->numeric()
                    ->default(null),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'completed' => 'Completed'])
                    ->default('draft')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('published_by')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('published_at'),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
