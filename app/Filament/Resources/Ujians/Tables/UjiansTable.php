<?php

namespace App\Filament\Resources\Ujians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UjiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_mulai', 'desc')
            ->columns([
                TextColumn::make('judul')->searchable()->sortable()->limit(35),
                TextColumn::make('jenis')->badge()->colors([
                    'ujian' => 'warning',
                    'kuis' => 'info',
                    'latihan' => 'success',
                ]),
                TextColumn::make('mapel.nama')->label('Mapel')->sortable()->toggleable(),
                TextColumn::make('kelas.nama')->label('Kelas')->sortable()->toggleable(),
                TextColumn::make('durasi_menit')->label('Durasi')->suffix(' mnt')->toggleable(),
                TextColumn::make('soals_count')->label('Soal')->counts('soals')->badge(),
                TextColumn::make('waktu_mulai')->dateTime('d M Y, H:i')->sortable(),
                IconColumn::make('is_published')->boolean()->label('Terbit'),
            ])
            ->filters([
                SelectFilter::make('jenis')->options([
                    'ujian' => 'Ujian', 'kuis' => 'Kuis', 'latihan' => 'Latihan',
                ]),
                SelectFilter::make('mapel_id')->label('Mapel')->relationship('mapel', 'nama'),
                SelectFilter::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama'),
                TernaryFilter::make('is_published')->label('Terbit'),
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
