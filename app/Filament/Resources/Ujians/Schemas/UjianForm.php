<?php

namespace App\Filament\Resources\Ujians\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UjianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Ujian')->schema([
                    TextInput::make('judul')->required()->maxLength(200),
                    ToggleButtons::make('jenis')
                        ->inline()
                        ->default('ujian')
                        ->options([
                            'ujian' => 'Ujian',
                            'kuis' => 'Kuis',
                            'latihan' => 'Latihan',
                        ])
                        ->colors([
                            'ujian' => 'warning',
                            'kuis' => 'info',
                            'latihan' => 'success',
                        ]),
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
                ])->columns(2),

                Section::make('Waktu & Durasi')->schema([
                    TextInput::make('durasi_menit')
                        ->label('Durasi (menit)')
                        ->numeric()->minValue(1)->default(60)->required(),
                    DateTimePicker::make('waktu_mulai')->label('Waktu Mulai')->required(),
                    DateTimePicker::make('waktu_selesai')->label('Waktu Selesai')->required()->rule('after:waktu_mulai'),
                    TextInput::make('passcode')
                        ->label('Kode Akses (opsional)')
                        ->helperText('Kosongkan jika tidak perlu kode untuk memulai ujian.'),
                ])->columns(2),

                Section::make('Pengaturan')->schema([
                    Toggle::make('acak_soal')->label('Acak urutan soal (anti-cheat)')->default(false),
                    Toggle::make('acak_opsi')->label('Acak urutan pilihan ganda (anti-cheat)')->default(false),
                    Toggle::make('tampilkan_hasil')->label('Tampilkan nilai ke siswa setelah selesai')->default(false),
                    Toggle::make('is_published')->label('Terbitkan ujian')->default(false),
                ])->columns(2),

                Section::make('Deskripsi')->schema([
                    RichEditor::make('deskripsi')->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
                ]),
            ]);
    }
}
