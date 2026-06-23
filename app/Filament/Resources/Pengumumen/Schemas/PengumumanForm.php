<?php

namespace App\Filament\Resources\Pengumumen\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengumumanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengumuman')->schema([
                    TextInput::make('judul')->required()->maxLength(200),
                    Select::make('target_type')
                        ->label('Target')
                        ->default('semua')
                        ->options([
                            'semua' => 'Semua Pengguna',
                            'kelas' => 'Kelas Tertentu',
                            'guru' => 'Guru Saja',
                            'siswa' => 'Siswa Saja',
                        ]),
                    TextInput::make('target_id')
                        ->label('ID Target')
                        ->helperText('Hanya jika Target = Kelas Tertentu (isi ID kelas). Boleh dikosongkan.')
                        ->numeric(),
                    RichEditor::make('konten')
                        ->label('Isi Pengumuman')
                        ->required()
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'h2']),
                    Toggle::make('is_pinned')->label('Sematkan (tampil di atas)')->default(false),
                    Hidden::make('created_by')->default(fn () => auth()->id()),
                ])->columns(2),
            ]);
    }
}
