<?php

namespace App\Filament\Resources\PengumpulanTugas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengumpulanTugasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('siswa.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tugas.judul')
                    ->label('Tugas')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tugas.mapel.nama')
                    ->label('Mapel')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'terkirim',
                        'warning' => 'terlambat',
                        'success' => 'dinilai',
                        'danger' => 'belum',
                    ]),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Dikumpulkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'terkirim' => 'Terkirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Dinilai',
                    ]),
                SelectFilter::make('tugas')
                    ->relationship('tugas', 'judul', modifyQueryUsing: fn ($query) => $query->orderBy('judul'))
                    ->label('Tugas')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()->label('Nilai'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
