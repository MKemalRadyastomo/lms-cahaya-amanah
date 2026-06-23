<?php

namespace App\Filament\Resources\Kelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tingkat')
            ->columns([
                TextColumn::make('nama')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('tingkat')->badge()->colors([
                    'gray' => 'X',
                    'info' => 'XI',
                    'warning' => 'XII',
                ]),
                TextColumn::make('jurusan')->toggleable(),
                TextColumn::make('tahunAjaran.tahun')->label('Tahun Ajaran')->sortable(),
                TextColumn::make('walikelas.name')->label('Wali Kelas')->default('—')->toggleable(),
                TextColumn::make('siswa_count')->label('Siswa')->counts('siswa'),
                TextColumn::make('created_at')->dateTime('d M Y')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('tingkat')->options([
                    'X' => 'Kelas X',
                    'XI' => 'Kelas XI',
                    'XII' => 'Kelas XII',
                ]),
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'tahun'),
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
