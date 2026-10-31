<?php

namespace App\Filament\Resources\ZakatRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ZakatRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options([
            'fitrah' => 'Fitrah',
            'maal' => 'Maal',
            'profesi' => 'Profesi',
            'fidyah' => 'Fidyah',
            'other' => 'Other',
        ])
                    ->default('fitrah')
                    ->required(),
                TextInput::make('period_year')
                    ->required()
                    ->numeric(),
                TextInput::make('period_label')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('payer_name')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
