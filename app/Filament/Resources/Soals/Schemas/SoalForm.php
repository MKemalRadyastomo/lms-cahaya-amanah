<?php

namespace App\Filament\Resources\Soals\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Soal')->schema([
                    ToggleButtons::make('tipe')
                        ->label('Tipe Soal')
                        ->inline()
                        ->live()
                        ->default('pg')
                        ->options([
                            'pg' => 'Pilihan Ganda',
                            'essay' => 'Esai',
                        ])
                        ->colors([
                            'pg' => 'info',
                            'essay' => 'warning',
                        ]),
                    Textarea::make('pertanyaan')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('poin')
                        ->numeric()->minValue(0)->default(1)->required()
                        ->helperText('Poin untuk soal ini (contoh: 20)'),
                ])->columns(2),

                Section::make('Pilihan Jawaban')->schema([
                    Repeater::make('opsi')
                        ->label('Pilihan')
                        ->simple(TextInput::make('value')->label('Teks Opsi'))
                        ->reorderable()
                        ->defaultItems(4)
                        ->addActionLabel('Tambah Pilihan')
                        ->visible(fn ($get) => $get('tipe') === 'pg')
                        ->helperText('Tulis semua pilihan. Isi "Jawaban Benar" di bawah dengan salah satu teks persis.'),
                    TextInput::make('jawaban_benar')
                        ->label('Jawaban Benar')
                        ->helperText('Untuk PG: isi persis salah satu teks pilihan. Untuk esai: isi kunci jawaban.')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
