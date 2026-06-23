<?php

namespace App\Filament\Resources\Materis\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MateriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Materi')->schema([
                    TextInput::make('judul')->required()->maxLength(200)->live(onBlur: true),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(250)
                        ->helperText('URL unik materi, isi bebas (huruf/angka/tanda hubung).'),
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
                    TextInput::make('video_url')
                        ->label('URL Video YouTube')
                        ->url()
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->columnSpanFull(),
                    RichEditor::make('konten')
                        ->label('Isi Materi')
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'h2', 'blockquote']),
                ])->columns(2),

                Section::make('Pengaturan')->schema([
                    Toggle::make('is_published')->label('Tampilkan ke siswa')->default(true),
                ]),
            ]);
    }
}
