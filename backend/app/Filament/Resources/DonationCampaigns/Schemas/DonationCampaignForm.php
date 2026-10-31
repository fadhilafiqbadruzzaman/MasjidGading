<?php

namespace App\Filament\Resources\DonationCampaigns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DonationCampaignForm
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
                TextInput::make('target_amount')
                    ->numeric()
                    ->default(null),
                TextInput::make('collected_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Select::make('status')
                    ->options([
            'active' => 'Active',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('active')
                    ->required(),
                FileUpload::make('image_path')
                    ->image(),
                TextInput::make('admin_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
