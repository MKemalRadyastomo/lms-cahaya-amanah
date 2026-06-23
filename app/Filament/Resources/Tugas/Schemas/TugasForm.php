<?php

namespace App\Filament\Resources\Tugas\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TugasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Tugas')->schema([
                    TextInput::make('judul')->required()->maxLength(200),
                    Select::make('mapel_id')
                        ->label('Mata Pelajaran')
                        ->relationship('mapel', 'nama')
                        ->required()->searchable()->preload(),
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->relationship('kelas', 'nama')
                        ->required()->searchable()->preload(),
                    Select::make('guru_id')
                        ->label('Guru')
                        ->relationship('guru', 'name', modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_GURU))
                        ->default(fn () => auth()->id())
                        ->required()->searchable()->preload(),
                    DateTimePicker::make('deadline')
                        ->label('Tenggat Waktu')
                        ->required()
                        ->minDate(now()),
                    TextInput::make('poin_max')
                        ->label('Poin Maksimal')
                        ->numeric()->minValue(1)->maxValue(100)->default(100)->required(),
                    RichEditor::make('deskripsi')
                        ->label('Deskripsi / Instruksi')
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'h2']),
                ])->columns(2),

                Section::make('Pengaturan')->schema([
                    Toggle::make('is_published')->label('Tampilkan ke siswa')->default(true),
                ]),
            ]);
    }
}
