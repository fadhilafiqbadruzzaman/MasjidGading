<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
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
                TextInput::make('location')
                    ->required(),
                DatePicker::make('event_date')
                    ->required(),
                TimePicker::make('event_time')
                    ->required(),
                DatePicker::make('end_date'),
                TimePicker::make('end_time'),
                TextInput::make('organizer')
                    ->required(),
                TextInput::make('capacity')
                    ->numeric()
                    ->default(null),
                Toggle::make('is_registration_open')
                    ->required(),
                Select::make('status')
                    ->options([
            'planned' => 'Planned',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('planned')
                    ->required(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
