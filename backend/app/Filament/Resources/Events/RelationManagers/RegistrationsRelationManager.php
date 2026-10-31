<?php

namespace App\Filament\Resources\Events\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Daftar Jamaah yang Mendaftar';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Jamaah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('user.phone')
                    ->label('No. WhatsApp / HP')
                    ->searchable()
                    ->default('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'registered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'registered' => 'Terdaftar',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),
                TextColumn::make('registered_at')
                    ->label('Waktu Pendaftaran')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->default('-')
                    ->limit(30),
            ])
            ->defaultSort('registered_at', 'desc');
    }
}
