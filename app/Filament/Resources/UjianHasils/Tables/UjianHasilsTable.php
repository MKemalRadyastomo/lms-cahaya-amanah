<?php

namespace App\Filament\Resources\UjianHasils\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UjianHasilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('waktu_selesai', 'desc')
            ->columns([
                TextColumn::make('siswa.name')->label('Siswa')->searchable()->sortable(),
                TextColumn::make('ujian.judul')->label('Ujian')->searchable()->sortable()->limit(30),
                TextColumn::make('ujian.mapel.nama')->label('Mapel')->sortable()->toggleable(),
                TextColumn::make('status')->badge()->colors([
                    'selesai' => 'success',
                    'sedang' => 'warning',
                    'belum_mulai' => 'gray',
                ]),
                TextColumn::make('nilai')->label('Nilai')->sortable()->weight('font-bold')->placeholder('-'),
                TextColumn::make('waktu_mulai')->label('Mulai')->dateTime('d M Y, H:i')->toggleable(),
                TextColumn::make('waktu_selesai')->label('Selesai')->dateTime('d M Y, H:i')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('ujian_id')->label('Ujian')->relationship('ujian', 'judul'),
                SelectFilter::make('status')->options([
                    'selesai' => 'Selesai',
                    'sedang' => 'Sedang Dikerjakan',
                    'belum_mulai' => 'Belum Mulai',
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
