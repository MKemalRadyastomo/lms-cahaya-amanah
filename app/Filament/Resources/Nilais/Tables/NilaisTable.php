<?php

namespace App\Filament\Resources\Nilais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NilaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('siswa.name')->label('Siswa')->searchable()->sortable(),
                TextColumn::make('mapel.nama')->label('Mapel')->searchable()->sortable(),
                TextColumn::make('kelas.nama')->label('Kelas')->sortable()->toggleable(),
                TextColumn::make('semester')->badge()->colors(['ganjil' => 'info', 'genap' => 'success']),
                TextColumn::make('nilai_tugas')->label('Tugas')->toggleable(),
                TextColumn::make('nilai_ujian')->label('Ujian')->toggleable(),
                TextColumn::make('nilai_akhir')->label('Akhir')->sortable()->weight('font-bold'),
                TextColumn::make('predikat')->badge()->colors([
                    'A' => 'success', 'B' => 'info', 'C' => 'warning', 'D' => 'danger', 'E' => 'gray',
                ]),
            ])
            ->filters([
                SelectFilter::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama'),
                SelectFilter::make('mapel_id')->label('Mapel')->relationship('mapel', 'nama'),
                SelectFilter::make('semester')->options(['ganjil' => 'Ganjil', 'genap' => 'Genap']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
