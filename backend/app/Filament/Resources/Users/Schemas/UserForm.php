<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('avatar')
                    ->default(null),
                Select::make('role')
                    ->label('Peran / Hak Akses')
                    ->options(function () {
                        if (auth()->user()?->isSuperAdmin()) {
                            return [
                                'super_admin' => 'Super Admin',
                                'admin_masjid' => 'Admin Masjid',
                                'marbot' => 'Marbot (Petugas Pemeliharaan)',
                                'jamaah' => 'Jamaah',
                            ];
                        }
                        // Admin Masjid hanya bisa membuat / memberi gelar Marbot atau Jamaah
                        return [
                            'marbot' => 'Marbot (Petugas Pemeliharaan)',
                            'jamaah' => 'Jamaah',
                        ];
                    })
                    ->default('marbot')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
