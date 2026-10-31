<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('location')
                    ->default(null),
                Select::make('condition')
                    ->options(['good' => 'Good', 'fair' => 'Fair', 'needs_repair' => 'Needs repair', 'damaged' => 'Damaged'])
                    ->default('good')
                    ->required(),
                DatePicker::make('last_maintained_at'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
