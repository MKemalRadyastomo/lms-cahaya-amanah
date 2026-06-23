<?php

namespace App\Filament\Resources\Tugas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TugasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('deadline', 'desc')
            ->columns([
                TextColumn::make('judul')->searchable()->sortable()->limit(40),
                TextColumn::make('mapel.nama')->label('Mapel')->sortable()->toggleable(),
                TextColumn::make('kelas.nama')->label('Kelas')->sortable()->toggleable(),
                TextColumn::make('deadline')->dateTime('d M Y, H:i')->sortable(),
                TextColumn::make('pengumpulan_tugas_count')->label('Pengumpulan')->counts('pengumpulanTugas')->badge(),
                IconColumn::make('is_published')->boolean()->label('Tampil'),
            ])
            ->filters([
                SelectFilter::make('mapel_id')->label('Mapel')->relationship('mapel', 'nama'),
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
