<?php

namespace App\Filament\Resources\SocialPrograms\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Daftar Pengajuan Bantuan Sosial Jamaah';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('applicant_name')
            ->columns([
                TextColumn::make('applicant_name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->label('No. HP / WA')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(25),
                TextColumn::make('requested_amount')
                    ->label('Kebutuhan Biaya')
                    ->money('idr', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'distributed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Review',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'distributed' => 'Telah Disalurkan',
                        default => $state,
                    }),
                TextColumn::make('reason')
                    ->label('Alasan / Kondisi')
                    ->limit(30)
                    ->tooltip(fn ($record): string => $record->reason ?? ''),
                TextColumn::make('notes')
                    ->label('Catatan Pengurus')
                    ->default('-')
                    ->limit(25),
                TextColumn::make('created_at')
                    ->label('Waktu Diajukan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('review')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->form([
                        Select::make('status')
                            ->label('Keputusan Verifikasi')
                            ->options([
                                'approved' => 'Setujui Permohonan',
                                'rejected' => 'Tolak Permohonan',
                                'distributed' => 'Sudah Disalurkan',
                            ])
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan Hasil Survei / Alasan')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'notes' => $data['notes'] ?? null,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                    }),
            ]);
    }
}
