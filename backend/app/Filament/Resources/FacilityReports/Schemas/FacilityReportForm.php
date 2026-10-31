<?php

namespace App\Filament\Resources\FacilityReports\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FacilityReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('task_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('reported_by')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->default(null),
                Select::make('condition')
                    ->options(['good' => 'Good', 'fair' => 'Fair', 'needs_repair' => 'Needs repair', 'damaged' => 'Damaged'])
                    ->default('needs_repair')
                    ->required(),
                FileUpload::make('image_path')
                    ->image(),
                Select::make('status')
                    ->options(['open' => 'Open', 'acknowledged' => 'Acknowledged', 'resolved' => 'Resolved'])
                    ->default('open')
                    ->required(),
                TextInput::make('acknowledged_by')
                    ->numeric()
                    ->default(null),
                TextInput::make('resolved_by')
                    ->numeric()
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
