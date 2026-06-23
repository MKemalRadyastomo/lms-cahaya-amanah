<?php

namespace App\Filament\Resources\Mapels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MapelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mata Pelajaran')->schema([
                    TextInput::make('kode')
                        ->label('Kode')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                    TextInput::make('nama')
                        ->label('Nama Mata Pelajaran')
                        ->required()
                        ->maxLength(100),
                    Select::make('jenjang')
                        ->options([
                            'X' => 'Kelas X',
                            'XI' => 'Kelas XI',
                            'XII' => 'Kelas XII',
                            'umum' => 'Umum (semua jenjang)',
                        ]),
                    TextInput::make('kkm')
                        ->label('KKM')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(70)
                        ->required(),
                ])->columns(2),
            ]);
    }
}
