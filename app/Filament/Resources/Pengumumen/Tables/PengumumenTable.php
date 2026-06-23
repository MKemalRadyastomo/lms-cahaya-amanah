<?php

namespace App\Filament\Resources\Pengumumen\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengumumenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('is_pinned', 'desc')
            ->columns([
                TextColumn::make('judul')->searchable()->sortable()->limit(50),
                TextColumn::make('target_type')
                    ->label('Target')
                    ->badge()
                    ->colors([
                        'semua' => 'info',
                        'kelas' => 'warning',
                        'guru' => 'success',
                        'siswa' => 'gray',
                    ]),
                IconColumn::make('is_pinned')->label('Semat')->boolean(),
                TextColumn::make('creator.name')->label('Dibuat Oleh')->toggleable(),
                TextColumn::make('created_at')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('target_type')->options([
                    'semua' => 'Semua', 'kelas' => 'Kelas', 'guru' => 'Guru', 'siswa' => 'Siswa',
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
