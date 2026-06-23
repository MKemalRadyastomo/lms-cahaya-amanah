<?php

namespace App\Filament\Resources\Jadwals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JadwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('hari')
            ->columns([
                TextColumn::make('kelas.nama')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('mapel.nama')->label('Mapel')->searchable()->sortable(),
                TextColumn::make('guru.name')->label('Guru')->searchable()->toggleable(),
                TextColumn::make('hari')->badge()->colors([
                    'gray' => 'senin',
                    'info' => 'selasa',
                    'success' => 'rabu',
                    'warning' => 'kamis',
                    'danger' => 'jumat',
                    'pink' => 'sabtu',
                    'purple' => 'minggu',
                ]),
                TextColumn::make('jam_mulai')->label('Mulai'),
                TextColumn::make('jam_selesai')->label('Selesai'),
                TextColumn::make('ruang')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama'),
                SelectFilter::make('hari')->options([
                    'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
                    'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu',
                ]),
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
