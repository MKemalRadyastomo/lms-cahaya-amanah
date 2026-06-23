<?php

namespace App\Filament\Resources\TahunAjarans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TahunAjaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tahun Ajaran')->schema([
                    TextInput::make('tahun')
                        ->label('Tahun Ajaran')
                        ->required()
                        ->placeholder('Contoh: 2024/2025')
                        ->maxLength(20),
                    ToggleButtons::make('semester')
                        ->required()
                        ->inline()
                        ->default('ganjil')
                        ->options([
                            'ganjil' => 'Ganjil',
                            'genap' => 'Genap',
                        ])
                        ->colors([
                            'ganjil' => 'info',
                            'genap' => 'success',
                        ]),
                    Toggle::make('is_active')
                        ->label('Aktif (tahun ajaran berjalan)')
                        ->helperText('Hanya satu tahun ajaran yang sebaiknya aktif.'),
                ])->columns(2),
            ]);
    }
}
