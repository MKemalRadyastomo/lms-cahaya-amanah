<?php

namespace App\Filament\Resources\Pengampus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengampusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guru.name')->label('Guru')->searchable()->sortable(),
                TextColumn::make('mapel.nama')->label('Mata Pelajaran')->searchable()->sortable(),
                TextColumn::make('kelas.nama')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('tahunAjaran.tahun')->label('Tahun Ajaran')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('guru_id')->label('Guru')->relationship('guru', 'name'),
                SelectFilter::make('mapel_id')->label('Mata Pelajaran')->relationship('mapel', 'nama'),
                SelectFilter::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama'),
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
