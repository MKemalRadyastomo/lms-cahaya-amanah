<?php

namespace App\Filament\Resources\Materis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MaterisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('judul')->searchable()->sortable()->limit(40),
                TextColumn::make('mapel.nama')->label('Mapel')->sortable()->toggleable(),
                TextColumn::make('kelas.nama')->label('Kelas')->sortable()->toggleable(),
                TextColumn::make('guru.name')->label('Guru')->toggleable(),
                IconColumn::make('is_published')->boolean()->label('Tampil'),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('mapel_id')->label('Mapel')->relationship('mapel', 'nama'),
                SelectFilter::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama'),
                TernaryFilter::make('is_published')->label('Tampil'),
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
