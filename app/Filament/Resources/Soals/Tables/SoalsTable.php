<?php

namespace App\Filament\Resources\Soals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->columns([
                TextColumn::make('ujian.judul')->label('Ujian')->limit(30)->searchable(),
                TextColumn::make('tipe')->badge()->colors([
                    'pg' => 'info',
                    'essay' => 'warning',
                ]),
                TextColumn::make('pertanyaan')->limit(60)->wrap(),
                TextColumn::make('poin')->sortable()->badge()->color('gray'),
                TextColumn::make('created_at')->dateTime('d M Y')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('ujian_id')->label('Ujian')->relationship('ujian', 'judul'),
                SelectFilter::make('tipe')->options([
                    'pg' => 'Pilihan Ganda',
                    'essay' => 'Esai',
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
