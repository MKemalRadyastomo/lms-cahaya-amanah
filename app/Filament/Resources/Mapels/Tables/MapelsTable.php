<?php

namespace App\Filament\Resources\Mapels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MapelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nama')
            ->columns([
                TextColumn::make('kode')->badge()->color('gray')->searchable(),
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('jenjang')->toggleable()->badge(),
                TextColumn::make('kkm')->label('KKM')->sortable(),
                TextColumn::make('created_at')->dateTime('d M Y')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('jenjang')->options([
                    'X' => 'Kelas X',
                    'XI' => 'Kelas XI',
                    'XII' => 'Kelas XII',
                    'umum' => 'Umum',
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
