<?php

namespace App\Filament\Resources\Kelas\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kelas')->schema([
                    TextInput::make('nama')
                        ->label('Nama Kelas')
                        ->required()
                        ->placeholder('Contoh: X IPA 1')
                        ->maxLength(50),
                    Select::make('tingkat')
                        ->required()
                        ->options([
                            'X' => 'Kelas X',
                            'XI' => 'Kelas XI',
                            'XII' => 'Kelas XII',
                        ]),
                    TextInput::make('jurusan')->maxLength(50)->placeholder('IPA / IPS / Bahasa (opsional)'),
                    Select::make('tahun_ajaran_id')
                        ->label('Tahun Ajaran')
                        ->relationship('tahunAjaran', 'tahun')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('walikelas_id')
                        ->label('Wali Kelas')
                        ->relationship('walikelas', 'name', modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_GURU))
                        ->searchable()
                        ->preload(),
                ])->columns(2),
            ]);
    }
}
